<?php

namespace App\Vectors\Contracts;

use App\Vectors\Vector;
use Illuminate\Support\Collection;

/**
 * Interface VectorModel
 *
 * @package App\Vectors\Contracts
 */
interface VectorModel
{
    /**
     * Retrieve this classes anchor point, which is the left(y: 0), top(x:0), foremost(z:0) point of this object.
     *
     * @return Vector
     */
    public function getAnchorPoint(): Vector;

    /**
     * Retrieve this classes dimensions which are width, height, and depth.
     *
     * @return Vector
     */
    public function getDimensions(): Vector;

    /**
     * Apply new vector values onto this class
     *
     * @param Vector $vector
     *
     * @return $this
     */
    public function setAnchorPoint(Vector $vector): self;

    /**
     * Return a collection of a rectangular slice of this object (plane of $rowAxis, $columnAxis);
     *
     * @param Vector $anchor
     * @param Vector $dimensions
     * @param string $rowAxis
     * @param string $columnAxis
     *
     * @return Collection
     */
    public function getPlanarPoints(Vector $anchor, Vector $dimensions, string $rowAxis = 'x', string $columnAxis = 'y'): Collection;

    /**
     * Return a collection of points along a line
     *
     * @param Vector $start
     * @param Vector $end
     *
     * @return Collection
     */
    public function getPointsLine(Vector $start, Vector $end): Collection;

    /**
     * Return a collection of all the vectors consumed by this object.
     * NOTE: This does not yet implement vertices on in three dimensions.
     *
     * @return Collection
     */
    public function getVectors(): Collection;
}
