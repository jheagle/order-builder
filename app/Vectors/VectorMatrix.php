<?php

namespace App\Vectors;

use App\Vectors\Traits\HasVectors;
use Illuminate\Support\Collection;

/**
 * Manage a matrix where vectors can be stored.
 *
 * @package App\Vectors
 */
class VectorMatrix extends Collection
{
    use HasVectors;

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
     * Apply a Vector so a specified position in the matrix.
     *
     * @param Vector $vector
     *
     * @return Vector
     *
     * @throws VectorException
     */
    public function assignVector(Vector $vector): Vector
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
    public function assignVectors(Collection $vectors): self
    {
        $vectors->each([$this, 'assignVector']);
        return $this;
    }

    /**
     * @param Collection $vectors
     *
     * @return bool
     */
    public function canUseVectors(Collection $vectors): bool
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
    public function create(): self
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
     * @return Collection
     */
    public function getAvailableVectors(): Collection
    {
        return $this->getMatrixVectors()->filter(fn(Vector $vector) => $vector->getBelongsTo() === $this);
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
    public function getVector(int $x = 0, int $y = 0, int $z = 0): Vector
    {
        if (!$this->hasVector($x, $y, $z)) {
        }
        return $this->get($z)->get($y)->get($x);
    }

    /**
     * @return Collection
     */
    public function getMatrixVectors(): Collection
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
     * Check if a set of coordinates exist in this Matrix
     *
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return bool
     */
    public function hasVector(int $x = 0, int $y = 0, int $z = 0): bool
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
    public function removeVector(int $x = 0, int $y = 0, int $z = 0): self
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
    private function throwException($x = 0, $y = 0, $z = 0): void
    {
        throw new \OutOfBoundsException(
            "Vector ('x': {$x}, 'y': {$y}, 'x': {$z}) "
            . "not found in Matrix ('width': {$this->width}, 'height': {$this->height}, 'depth': {$this->depth})"
        );
    }
}
