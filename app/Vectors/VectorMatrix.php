<?php

namespace App\Vectors;

use Exception;
use Illuminate\Support\Collection;
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

    private int $x = 0;

    private int $y = 0;

    private int $z = 0;

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
     * Assign next available space to the Print Sheet Item
     *
     * @param VectorModel $vectorModel
     *
     * @return self
     *
     * @throws Exception
     */
    final public function assignAvailablePosition(VectorModel $vectorModel): self
    {
        if ($vectorModel->getVectors()->count() > $this->getAvailableVectors()->count()) {
            throw new Exception('There is no available space for ' . get_class($vectorModel));
        }
        $anchorPoint = $this->getAvailableVectors()->first(
            fn (Vector $availVector) => $this->canUseVectors($vectorModel->setAnchorPoint($availVector)->getVectors())
        );
        if (is_null($anchorPoint)) {
            $vectorModel->setAnchorPoint($anchorPoint);
            throw new Exception('There is no available space to set anchor for ' . get_class($vectorModel));
        }
        return $this->assignVectors($vectorModel->getVectors());
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
     * Get all of the vectors within this matrix that are not occupied.
     *
     * @return Collection
     */
    final public function getAvailableVectors(): Collection
    {
        return $this->getMatrixVectors()->filter(fn(Vector $vector) => $vector->getBelongsTo() === $this);
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
     * Throw standard out of bounds exception when a Vector does not exist within this Matrix
     *
     * @param int $x
     * @param int $y
     * @param int $z
     */
    private function throwException(int $x = 0, int $y = 0, int $z = 0): void
    {
        throw new OutOfBoundsException(
            "Vector ('x': {$x}, 'y': {$y}, 'x': {$z}) "
            . "not found in Matrix ('width': {$this->width}, 'height': {$this->height}, 'depth': {$this->depth})"
        );
    }
}
