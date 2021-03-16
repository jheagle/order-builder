<?php

namespace App\Vectors;

/**
 * Store a whole number point in space.
 *
 * @package App\Vectors
 */
class Vector
{
    public int $x = 0;
    public int $y = 0;
    public int $z = 0;

    protected mixed $belongsTo;

    /**
     * Generate a Vector instance.
     *
     * @param int $x
     * @param int $y
     * @param int $z
     * @param mixed|null $belongsTo
     */
    public function __construct(int $x = 0, int $y = 0, int $z = 0, $belongsTo = null)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->belongsTo = $belongsTo;
    }

    /**
     * Return a new vector with summed coordinates.
     *
     * @param Vector $vector
     * @param null $belongsTo
     *
     * @return Vector
     */
    public function add(Vector $vector, $belongsTo = null): Vector
    {
        return new static(
            $this->x + $vector->x,
            $this->y + $vector->y,
            $this->z + $vector->z,
            $belongsTo
        );
    }

    /**
     * Return a new Vector with delta of each coordinate.
     *
     * @param Vector $vector
     *
     * @return Vector
     */
    public function diff(Vector $vector): Vector
    {
        return new static(
            $vector->x - $this->x,
            $vector->y - $this->y,
            $vector->z - $this->z,
        );
    }

    /**
     * Check if the coordinates match this Vector.
     *
     * @param Vector $vector
     *
     * @return bool
     */
    public function equals(Vector $vector): bool
    {
        return $this->x === $vector->x
            && $this->y === $vector->y
            && $this->z === $vector->z;
    }

    /**
     * Return class or object this Vector belongs to.
     *
     * @return mixed
     */
    public function getBelongsTo()
    {
        return $this->belongsTo;
    }

    /**
     * Retrieve a direction which is a Vector with only one non-zero coordinate.
     *
     * @param Vector $vector
     *
     * @return Vector
     */
    public function getDirection(Vector $vector): Vector
    {
        $diff = $this->diff($vector);
        return new static(
            $diff->x !== 0
                ? ($diff->x > 0 ? 1 : -1)
                : 0,
            $diff->y !== 0
                ? ($diff->y > 0 ? 1 : -1)
                : 0,
            $diff->z !== 0
                ? ($diff->z > 0 ? 1 : -1)
                : 0,
        );
    }

    /**
     * Given a Vecor of lengths, find the Vector on the opposite side of this shape.
     *
     * @param Vector $dimensions
     * @param Vector|null $direction
     * @param null $belongsTo
     *
     * @return Vector
     */
    public function getEndPoint(Vector $dimensions, Vector $direction = null, $belongsTo = null): Vector
    {
        if (is_null($direction)) {
            $direction = new Vector(1, 1, 1);
        }
        $xValue = $dimensions->x
            ? $this->x + ($direction->x * abs($dimensions->x)) - 1
            : 0;
        $yValue = $dimensions->y
            ? $this->y + ($direction->y * abs($dimensions->y)) - 1
            : 0;
        $zValue = $dimensions->z
            ? $this->z + ($direction->z * abs($dimensions->z)) - 1
            : 0;
        return new static($xValue, $yValue, $zValue, $belongsTo);
    }

    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
        ];
    }
}
