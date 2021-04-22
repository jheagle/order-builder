<?php

namespace App\Vectors;

use Exception;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use OutOfBoundsException;

/**
 * Manage a matrix where vectors can be stored.
 *
 * @package App\Vectors
 */
class VectorMatrix extends Collection
{
    public array $coordinates = [
        'x' => 'x',
        'y' => 'y',
        'z' => 'z',
    ];

    public array $dimensions = [
        'width' => 'width',
        'height' => 'height',
        'depth' => 'depth',
    ];

    public int $width = 0;

    public int $height = 0;

    public int $depth = 0;

    /**
     * VectorMatrix constructor.
     *
     * @param int $width
     * @param int $height
     * @param int $depth
     */
    public function __construct(int $width = 1, int $height = 1, int $depth = 1)
    {
        parent::__construct();
        $this->width = $width;
        $this->height = $height;
        $this->depth = $depth;
    }

    /**
     * Check if the given dimensions will fit within the remaining available space.
     *
     * @param array $dimensions
     *
     * @return bool
     */
    private function fitsAvailableSpace(array $dimensions): bool
    {
        $groups = $this->getContiguousAvailableVectors();
        // TODO: For each group get the shortest dimensions and build a matrix, then check canFitDimensions, return on first positive, or return false
    }

    /**
     * Assign next available space to the Print Sheet Item
     *
     * @param Collection $vectorItems
     *
     * @return self
     *
     * @throws Exception
     */
    final public function assignAvailablePositions(Collection $vectorItems): self
    {
        $this->sortVectorItems($vectorItems)->each(
            fn(VectorModel $vectorItem) => $this->assignAvailablePosition($vectorItem)
        );
        return $this;
    }

    /**
     * Apply a Vector so a specified position in the matrix.
     *
     * @param Vector $vector
     *
     * @return Vector
     */
    final public function assignVector(Vector $vector): Vector
    {
        if (!$this->hasVector(...$vector->toArray())) {
            $this->throwException(...$vector->toArray());
        }
        $this->get($vector->z)
            ->get($vector->y)
            ->put($vector->x, $vector);
        return $vector;
    }

    /**
     * Assign a collection of vectors to their respective positions in the matrix.
     *
     * @param Collection $vectors
     *
     * @return self
     */
    final public function assignVectors(Collection $vectors): self
    {
        $vectors->each([$this, 'assignVector']);
        return $this;
    }

    /**
     * Check if given dimensions will fit into this matrix.
     *
     * @param array $dimensions
     *
     * @return bool
     */
    private function canFitDimensions(array $dimensions): bool
    {
        $matrixArea = self::WIDTH * self::HEIGHT * self::DEPTH;
        $widthDivisor = floor(self::WIDTH / $dimensions['width']);
        $heightDivisor = floor(self::HEIGHT / $dimensions['height']);
        $depthDivisor = floor(self::DEPTH / $dimensions['depth']);
        $maxWidth = $widthDivisor * $dimensions['width'];
        $maxHeight = $heightDivisor * $dimensions['height'];
        $maxDepth = $depthDivisor * $dimensions['depth'];
        $maxArea = $maxWidth * $maxHeight * $maxDepth;
        $remainingArea = $matrixArea - $maxArea;
        $remainingHeight = floor($matrixArea / $maxWidth);
        return $matrixArea >= $remainingArea && $dimensions['height'] <= $remainingHeight;
    }

    /**
     * Check a set of vectors to see if they fit within the available vectors of this matrix
     *
     * @param Collection $vectors
     *
     * @return bool
     */
    final public function canUseVectors(Collection $vectors): bool
    {
        $availVectors = $this->getAvailableVectors();
        return $vectors->every(
            fn($vector) => $availVectors->contains(
                fn($avail) => $avail->equals($vector)
            )
        );
    }

    /**
     * Build the Matrix based on set dimensions.
     *
     * @return self
     */
    final public function create(): self
    {
        for ($z = 0; $z < $this->depth; ++$z) {
            $layer = new Collection();
            for ($y = 0; $y < $this->height; ++$y) {
                $row = new Collection();
                for ($x = 0; $x < $this->width; ++$x) {
                    $row->push(new Vector($x, $y, $z, $this));
                }
                $layer->push($row);
            }
            $this->push($layer);
        }
        return $this;
    }

    /**
     * Get all contiguous points in the positive direction along each axis from start point, optionally apply custom callback.
     *
     * @param Vector $startPoint
     * @param callable|null $endCondition
     *
     * @return Collection
     */
    final public function findAllContiguousPoints(Vector $startPoint, callable $endCondition = null): Collection
    {
        if (is_null($endCondition)) {
            $endCondition = static fn(Vector $v) => true;
        }
        if (!$this->hasVector(...$startPoint->toArray())) {
            return Collection::make();
        }
        $startPoint = $this->getVector(...$startPoint->toArray());
        if (!$endCondition($startPoint)) {
            return Collection::make();
        }
        $contiguousPoints = Collection::make([$startPoint]);
        $adjacentPoints = [$this->processContiguousPoint($startPoint, $endCondition)];
        $nextAxis = [
            'x' => 'y',
            'y' => 'z',
            'z' => 'x',
        ];
        while (count($adjacentPoints)) {
            $nextVectors = array_shift($adjacentPoints);
            foreach ($nextVectors as $axis => $vectors) {
                if (!count($vectors)) {
                    continue;
                }
                $coordinates = $vectors[0]->toArray();
                ++$coordinates[$nextAxis[$axis]];
                $contiguousPoints->push(...$vectors);
                $contiguousPoints = $contiguousPoints->unique();
                if (!$this->hasVector(...$coordinates)) {
                    continue;
                }
                $nextVector = $this->getVector(...$coordinates);
                if (!$endCondition($nextVector)) {
                    continue;
                }
                $contiguousPoints->push($nextVector);
                array_push($adjacentPoints, $this->processContiguousPoint($nextVector, $endCondition));
            }
        }
        return $contiguousPoints;
    }

    /**
     * Get all of the vectors within this matrix that are not occupied.
     *
     * @return Collection
     */
    final public function getAvailableVectors(): Collection
    {
        return $this->getMatrixVectors()->filter([$this, 'isAvailableVector']);
    }

    /**
     * Find all vectors which are considered available within contiguous groups.
     *
     * @return Collection
     */
    final public function getContiguousAvailableVectors(): Collection
    {
        $allVectors = $this->getAvailableVectors()->values();
        $contiguousGroups = Collection::make();
        while ($allVectors->count()) {
            $group = $this->findAllContiguousPoints($allVectors->shift(), [$this, 'isAvailableVector']);
            foreach ($group as $gVector) {
                $key = $allVectors->search(fn(Vector $vector) => $gVector->equals($vector));
                if ($key === false) {
                    continue;
                }
                $allVectors->splice($key, 1);
                $allVectors = $allVectors->values();
            }
            $contiguousGroups->push($group);
        }
        return $contiguousGroups;
    }

    /**
     * Retrieve all of the distinct Vector Models occupying space in this matrix.
     *
     * @return Collection
     */
    final public function getDistinctOccupiers(): Collection
    {
        return $this->getMatrixVectors()
            ->filter(fn(Vector $vector) => $vector->getBelongsTo() !== $this && $vector->getBelongsTo() !== null)
            ->map(fn(Vector $vector) => $vector->getBelongsTo())
            ->unique(fn(VectorModel $vectorModel) => $vectorModel->getAnchorPoint()->toArray())
            ->values();
    }

    /**
     * Retrieve a collection of all vectors from this matrix
     *
     * @return Collection
     */
    final public function getMatrixVectors(): Collection
    {
        $vectors = new Collection();
        for ($z = 0; $z < $this->depth; ++$z) {
            $layer = $this->get($z);
            if (is_null($layer)) {
                continue;
            }
            for ($y = 0; $y < $this->height; ++$y) {
                $row = $layer->get($y);
                if (is_null($row)) {
                    continue;
                }
                $vectors = $vectors->concat($row);
            }
        }
        return $vectors;
    }

    /**
     * Retrieve a Vector by coordinates
     *
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return Vector
     */
    final public function getVector(int $x = 0, int $y = 0, int $z = 0): Vector
    {
        if (!$this->hasVector($x, $y, $z)) {
            $this->throwException($x, $y, $z);
        }
        return $this->get($z)->get($y)->get($x);
    }

    /**
     * Check if a set of coordinates exist in this Matrix
     *
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return bool
     */
    final public function hasVector(int $x = 0, int $y = 0, int $z = 0): bool
    {
        return $this->has($z)
            && $this->get($z)->has($y)
            && $this->get($z)->get($y)->has($x);
    }

    /**
     * Check if this vector is available or used.
     *
     * @param Vector $vector
     *
     * @return bool
     */
    #[Pure]
    final public function isAvailableVector(Vector $vector): bool
    {
        return $vector->getBelongsTo() === $this;
    }

    /**
     * Remove a Vector at coordinates
     *
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return $this
     */
    final public function removeVector(int $x = 0, int $y = 0, int $z = 0): self
    {
        if (!$this->hasVector($x, $y, $z)) {
            $this->throwException($x, $y, $z);
        }
        $this->get($z)->get($y)->get($x)->put($x, new Vector($x, $y, $z, $this));
        return $this;
    }

    /**
     * Assign next available space to the Print Sheet Item
     *
     * @param VectorModel $vectorModel
     *
     * @return self
     *
     * @throws Exception
     */
    private function assignAvailablePosition(VectorModel $vectorModel): self
    {
        if ($vectorModel->getVectors()->count() > $this->getAvailableVectors()->count()) {
            throw new Exception(sprintf(
                'Insufficient available space of %d for %s, (width: %d, height: %d, depth: %d)',
                $this->getAvailableVectors()->count(),
                get_class($vectorModel),
                ...array_values($vectorModel->getDimensions()->toArray())
            ));
        }
        $anchorPoint = $this->getAvailableVectors()->first(
            fn(Vector $availVector) => $this->canUseVectors($vectorModel->setAnchorPoint($availVector)->getVectors())
        );
        if (is_null($anchorPoint)) {
            $vectorModel->setAnchorPoint($anchorPoint);
            throw new Exception(sprintf(
                    'Insufficient available space of %d to set anchor for %s, (width: %d, height: %d, depth: %d)',
                    $this->getAvailableVectors()->count(),
                    get_class($vectorModel),
                    ...array_values($vectorModel->getDimensions()->toArray())
                )
            );
        }
        return $this->assignVectors($vectorModel->getVectors());
    }

    /**
     * Apply a function to each vector along each axis from this point (positive direction).
     *
     * @param Vector $vector
     * @param callable $endCondition
     *
     * @return array
     */
    private function processContiguousPoint(Vector $vector, callable $endCondition): array
    {
        $returnAdjacent = [];
        $directions = ['x', 'y', 'z'];
        foreach ($directions as $axis) {
            $returnAdjacent[$axis] = [];
            $coordinates = $vector->toArray();
            $nextVector = null;
            do {
                ++$coordinates[$axis];
                if (!$this->hasVector(...$coordinates)) {
                    $nextVector = null;
                    break;
                }
                $nextVector = $this->getVector(...$coordinates);
                if (!$endCondition($nextVector)) {
                    $nextVector = null;
                    break;
                }
                array_push($returnAdjacent[$axis], $nextVector);
            } while (!is_null($nextVector));
        }
        return $returnAdjacent;
    }

    /**
     * Take all of the Vector Model Items and sort them from largest perimeter to smallest.
     * Equal perimeter place largest width first.
     *
     * @param Collection $vectorItems
     *
     * @return Collection
     */
    private function sortVectorItems(Collection $vectorItems): Collection
    {
        return $vectorItems->sort(function (VectorModel $a, VectorModel $b) {
            if ($a->getDimensions()->equals($b->getDimensions())) {
                return 0;
            }
            $highestWidth = $a->getDimensions()->x > $b->getDimensions()->x ? $a : $b;
            $highestHeight = $a->getDimensions()->y > $b->getDimensions()->y ? $a : $b;
            $largestSide = $highestHeight->getDimensions()->y > $highestWidth->getDimensions()->x
                ? $highestHeight
                : $highestWidth;
            return $largestSide === $a ? -1 : 1;
        })->values();
    }

    /**
     * Throw standard out of bounds exception when a Vector does not exist within this Matrix
     *
     * @param int $x
     * @param int $y
     * @param int $z
     */
    private function throwException(int $x = 0, int $y = 0, int $z = 0): void
    {
        throw new OutOfBoundsException(
            "Vector ('x': $x, 'y': $y, 'x': $z) "
            . "not found in Matrix ('width': $this->width, 'height': $this->height, 'depth': $this->depth)"
        );
    }
}
