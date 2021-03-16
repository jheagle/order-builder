<?php

namespace App\Vectors;

use App\Vectors\Traits\HasVectors;
use Illuminate\Support\Collection;

/**
 * Manage a matrix where vectors can be stored.
 *
 * @package App\Vectors
 */
class VectorMatrix
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

    private Collection $matrix;

    /**
     * @param int $width
     * @param int $height
     * @param int $depth
     */
    public function __construct(int $width, int $height, int $depth = 1)
    {
        $this->width = $width;
        $this->height = $height;
        $this->depth = $depth;
        $this->matrix = new Collection();
        for ($z = 0; $z < $depth; ++$z) {
            $layer = new Collection();
            for ($y = 0; $y < $height; ++$y) {
                $row = new Collection();
                for ($x = 0; $x < $width; ++$x) {
                    $row->push(new Vector($x, $y, $z, $this));
                }
                $layer->push($row);
            }
            $this->matrix->push($layer);
        }
    }

    /**
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
     * @param Vector $vector
     *
     * @return Vector
     */
    public function assignVector(Vector $vector): Vector
    {
        $this->matrix->get($vector->z)->get($vector->y)->put($vector->x, $vector);
        return $vector;
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
            fn ($vector) => $availVectors->contains(
                fn ($avail) => $avail->equals($vector)
            )
        );
    }

    /**
     * @return Collection
     */
    public function getAvailableVectors(): Collection
    {
        return $this->getMatrixVectors()->filter(fn (Vector $vector) => !$vector->getBelongsTo() || $vector->getBelongsTo() === $this);
    }

    /**
     * @return Collection
     */
    public function getMatrixVectors(): Collection
    {
        $vectors = new Collection();
        for ($z = 0; $z < $this->depth; ++$z) {
            $layer = $this->matrix->get($z);
            for ($y = 0; $y < $this->height; ++$y) {
                $row = $layer->get($y);
                $vectors = $vectors->concat($row);
            }
        }
        return $vectors;
    }
}
