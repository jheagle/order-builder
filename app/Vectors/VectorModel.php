<?php

namespace App\Vectors;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Abstract class VectorModel
 *
 * @package App\Vectors
 * @method static Builder|VectorModel newModelQuery()
 * @method static Builder|VectorModel newQuery()
 * @method static Builder|VectorModel query()
 * @mixin Eloquent
 */
abstract class VectorModel extends Model
{
    protected ?Vector $anchorPoint = null;
    protected ?Collection $vectors = null;

    protected array $coordinates = [
        'x' => 'x',
        'y' => 'y',
    ];

    protected array $dimensions = [
        'width' => 'width',
        'height' => 'height',
    ];

    /**
     * Retrieve this classes anchor point, which is the left(y: 0), top(x:0), foremost(z:0) point of this object.
     *
     * @return Vector
     */
    final public function getAnchorPoint(): Vector
    {
        if (is_null($this->anchorPoint)) {
            $this->anchorPoint = new Vector(0, 0, 0, $this);
        }
        $xField = Arr::get($this->coordinates, 'x', 'x');
        $yField = Arr::get($this->coordinates, 'y', 'y');
        $zField = Arr::get($this->coordinates, 'z', 'z');
        $xValue = data_get($this, $xField, $this->anchorPoint->x);
        $yValue = data_get($this, $yField, $this->anchorPoint->y);
        $zValue = data_get($this, $zField, $this->anchorPoint->z);
        $vector = new Vector($xValue, $yValue, $zValue, $this);
        if (!$this->anchorPoint->equals($vector)) {
            $this->anchorPoint = $vector;
        }
        return $this->anchorPoint;
    }

    /**
     * Retrieve this classes dimensions which are width, height, and depth.
     *
     * @return Vector
     */
    final public function getDimensions(): Vector
    {
        $widthField = Arr::get($this->dimensions, 'width', 'width');
        $heightField = Arr::get($this->dimensions, 'height', 'height');
        $depthField = Arr::get($this->dimensions, 'depth', 'depth');
        return new Vector(
            $this->{$widthField} ?? 1,
            $this->{$heightField} ?? 1,
            $this->{$depthField} ?? 1,
        );
    }

    /**
     * Apply new vector values onto this class
     *
     * @param Vector|null $vector
     *
     * @return $this
     */
    final public function setAnchorPoint(?Vector $vector): self
    {
        if (is_null($vector)){
            $this->anchorPoint = null;
            return $this;
        }
        $xField = Arr::get($this->coordinates, 'x', 'x');
        $yField = Arr::get($this->coordinates, 'y', 'y');
        $zField = Arr::get($this->coordinates, 'z', 'z');
        $vector = new Vector($vector->x, $vector->y, $vector->z, $this);
        if (is_null($this->anchorPoint) || !$this->anchorPoint->equals($vector)) {
            $this->anchorPoint = $vector;
        }
        if (!is_null($this->{$xField})) {
            data_set($this, $xField, $vector->x);
        }
        if (!is_null($this->{$yField})) {
            data_set($this, $yField, $vector->y);
        }
        if (!is_null($this->{$zField})) {
            data_set($this, $zField, $vector->z);
        }
        $this->vectors = null;
        return $this;
    }

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
    final public function getPlanarPoints(Vector $anchor, Vector $dimensions, string $rowAxis = 'x', string $columnAxis = 'y'): Collection
    {
        $direction = new Vector(1, 1, 1);
        $endPoint = $anchor->getEndPoint($dimensions, $direction, $this);
        $planarPoints = new Collection();
        $columnEnd = new Vector(
            $columnAxis === 'x' ? $endPoint->x : $anchor->x,
            $columnAxis === 'y' ? $endPoint->y : $anchor->y,
            $columnAxis === 'z' ? $endPoint->z : $anchor->z,
        );
        $stepper = new Vector(
            $rowAxis === 'x' ? $endPoint->x : $anchor->x,
            $rowAxis === 'y' ? $endPoint->y : $anchor->y,
            $rowAxis === 'z' ? $endPoint->z : $anchor->z,
            $this
        );
        if ($anchor->equals($columnEnd)) {
            return $planarPoints->concat($this->getPointsLine($anchor, $stepper));
        }
        while (!$anchor->equals($columnEnd)) {
            $planarPoints = $planarPoints->concat($this->getPointsLine($anchor, $stepper));
            $anchor = $anchor->add($anchor->getDirection($columnEnd), $this);
            $stepper = new Vector(
                $rowAxis === 'x' ? $endPoint->x : $anchor->x,
                $rowAxis === 'y' ? $endPoint->y : $anchor->y,
                $rowAxis === 'z' ? $endPoint->z : $anchor->z,
                $this
            );
        }
        return $planarPoints->concat($this->getPointsLine($columnEnd, $stepper));
    }

    /**
     * Return a collection of points along a line
     *
     * @param Vector $start
     * @param Vector $end
     *
     * @return Collection
     */
    final public function getPointsLine(Vector $start, Vector $end): Collection
    {
        $line = new Collection();
        if ($start->equals($end)) {
            return $line->push($start);
        }
        while (!$start->equals($end)) {
            $line->push($start);
            $start = $start->add($start->getDirection($end), $this);
        }
        $line->push($end);
        return $line;
    }

    /**
     * Return a collection of all the vectors consumed by this object.
     * NOTE: This does not yet implement vertices on in three dimensions.
     *
     * @return Collection
     */
    final public function getVectors(): Collection
    {
        if (is_null($this->vectors)){
            $this->vectors = $this->getPlanarPoints($this->getAnchorPoint(), $this->getDimensions());
        }
        return $this->vectors;
    }
}
