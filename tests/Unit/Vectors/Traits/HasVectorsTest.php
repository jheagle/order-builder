<?php

namespace Tests\Unit\Vectors\Traits;

use Tests\TestCase;
use App\Vectors\Vector;
use App\Vectors\Traits\HasVectors;

/**
 * Test using a class with the HasVectors trait
 *
 * @package Tests\Unit\Vectors\Traits
 *
 * @group Unit
 * @group Vectors
 * @group Traits
 * @group HasVectors
 *
 * @coversDefaultClass HasVectors
 */
class HasVectorsTest extends TestCase
{
    /**
     * Given a class with HasVectors
     * When calling getAnchorPoint and coordinates are not set
     * Then a default vector with zeros will be returned
     *
     * @covers ::getAnchorPoint
     */
    final public function testGetAnchorPointWithNoCoordinatesReturnsDefaultVector(): void
    {
        $vectorsClass = $this->makeHasVectorsClass(['coordinates' => null]);
        self::assertTrue(
            $vectorsClass->getAnchorPoint()->equals(new Vector(0, 0, 0))
        );
    }

    /**
     * Given a class with HasVectors
     * When calling getAnchorPoint with default coordinate properties (x, y, z)
     * Then those properties will be used for getting anchor.
     *
     * @covers ::getAnchorPoint
     */
    final public function testGetAnchorPointWithSingleCoordinateDefaultsZeros(): void
    {
        $vectorsClass = $this->makeHasVectorsClass(['x' => 1, 'y' => 2, 'z' => 3]);
        self::assertTrue(
            $vectorsClass->getAnchorPoint()->equals(new Vector(1, 2, 3))
        );
    }

    /**
     * Given a class with HasVectors
     * When calling getAnchorPoint with custom fields configured
     * Then those properties will be used for getting anchor.
     *
     * @covers ::getAnchorPoint
     */
    final public function testGetAnchorPointReturnsVectorBasedOnCustomFields(): void
    {
        $vectorsClass = $this->makeHasVectorsClass([
            'coordinates' => [
                'x' => 'x_pos',
                'y' => 'y_pos',
                'z' => 'z_pos',
            ],
            'x_pos' => 1,
            'y_pos' => 2,
            'z_pos' => 3
        ]);
        self::assertTrue(
            $vectorsClass->getAnchorPoint()->equals(new Vector(1, 2, 3))
        );
    }

    /**
     * Given a class with HasVectors
     * When calling getDimensions and dimensions are not set
     * Then a default vector with ones will be returned
     *
     * @covers ::getDimensions
     */
    final public function testGetDimensionsWithNoCoordinatesReturnsDefaultVector(): void
    {
        $vectorsClass = $this->makeHasVectorsClass(['dimensions' => null]);
        self::assertTrue(
            $vectorsClass->getDimensions()->equals(new Vector(1, 1, 1))
        );
    }

    /**
     * Given a class with HasVectors
     * When calling getDimensions with default dimensions properties (width, height, depth)
     * Then those properties will be used for getting dimensions.
     *
     * @covers ::getDimensions
     */
    final public function testGetDimensionsWithSingleCoordinateDefaultsZeros(): void
    {
        $vectorsClass = $this->makeHasVectorsClass(['width' => 1, 'height' => 2, 'depth' => 3]);
        self::assertTrue(
            $vectorsClass->getDimensions()->equals(new Vector(1, 2, 3))
        );
    }

    /**
     * Given a class with HasVectors
     * When calling getDimensions with custom fields configured
     * Then those properties will be used for getting dimensions.
     *
     * @covers ::getDimensions
     */
    final public function testGetDimensionsReturnsVectorBasedOnCustomFields(): void
    {
        $vectorsClass = $this->makeHasVectorsClass([
            'dimensions' => [
                'width' => 'size_w',
                'height' => 'size_h',
                'depth' => 'size_d',
            ],
            'size_w' => 1,
            'size_h' => 2,
            'size_d' => 3
        ]);
        self::assertTrue(
            $vectorsClass->getDimensions()->equals(new Vector(1, 2, 3))
        );
    }

    /**
     * Given a class with HasVectors
     * When setting anchor with no coordinate configured
     * Then no field will be set except for the anchorPoint.
     *
     * @covers ::setAnchorPoint
     */
    final public function testSetAnchorPointsWithNoCoordinatesWillOnlySetAnchorPointField(): void
    {
        $vectorsClass = $this->makeHasVectorsClass(['coordinates' => null]);
        $anchor = new Vector(3, 2, 1);
        $vectorsClass->setAnchorPoint($anchor);
        self::assertTrue(
            $vectorsClass->getAnchorPoint()->equals($anchor)
        );
    }

    /**
     * Given a class with HasVectors
     * When setting anchor with coordinates configured
     * Then the associated coordinate field will be updated.
     *
     * @covers ::setAnchorPoint
     */
    final public function testSetAnchorPointsWithConfiguredCoordinatesSetsFields(): void
    {
        $vectorsClass = $this->makeHasVectorsClass([
            'coordinates' => ['x' => 'x'],
            'x' => 0,
        ]);
        $xValue = 3;
        $anchor = new Vector($xValue, 2, 1);
        $vectorsClass->setAnchorPoint($anchor);
        self::assertTrue(
            $vectorsClass->getAnchorPoint()->equals($anchor)
        );
        self::assertEquals($xValue, $vectorsClass->x);
    }

    /**
     * Given start and end points
     * When getPointsLine is called with the provided points
     * Then a collection of points between start and end (inclusive) will be returned.
     *
     * @dataProvider linePointsProvider
     *
     * @param array $start
     * @param array $end
     * @param array $expected
     *
     * @covers ::getPointsLine
     */
    final public function testGetPointsLine(array $start, array $end, array $expected): void
    {
        $startPoint = new Vector(...$start);
        $endPoint = new Vector(...$end);
        $vectorsClass = $this->makeHasVectorsClass();
        $results = $vectorsClass->getPointsLine($startPoint, $endPoint);

        self::assertCount(count($expected), $results);
        $resultsArray = $results->map(fn($result) => array_values($result->toArray()))->toArray();

        foreach ($expected as $point) {
            self::assertContains($point, $resultsArray);
        }
    }

    /**
     * Sample sets for getLinePoints
     */
    final public function linePointsProvider(): array
    {
        return [
            'start and end are equal return one point' => [
                'start' => [0, 0, 0],
                'end' => [0, 0, 0],
                'expected' => [[0, 0, 0]]
            ],
            'x axis end gets all x axis points' => [
                'start' => [0, 0, 0],
                'end' => [5, 0, 0],
                'expected' => [[0, 0, 0], [1, 0, 0], [2, 0, 0], [3, 0, 0], [4, 0, 0], [5, 0, 0]]
            ],
            'y axis end gets all y axis points' => [
                'start' => [0, 0, 0],
                'end' => [0, 5, 0],
                'expected' => [[0, 0, 0], [0, 1, 0], [0, 2, 0], [0, 3, 0], [0, 4, 0], [0, 5, 0]]
            ],
            'z axis end gets all z axis points' => [
                'start' => [0, 0, 0],
                'end' => [0, 0, 5],
                'expected' => [[0, 0, 0], [0, 0, 1], [0, 0, 2], [0, 0, 3], [0, 0, 4], [0, 0, 5]]
            ],
            'changes on two axis will have all step points' => [
                'start' => [0, 0, 0],
                'end' => [1, 3, 0],
                'expected' => [[0, 0, 0], [1, 1, 0], [1, 2, 0], [1, 3, 0]]
            ],
            'changes on two axis with large gaps has all steps' => [
                'start' => [0, 0, 0],
                'end' => [3, 6, 0],
                'expected' => [[0, 0, 0], [1, 1, 0], [2, 2, 0], [3, 3, 0], [3, 4, 0], [3, 5, 0], [3, 6, 0]]
            ],
            'changes on three axis will have all steps' => [
                'start' => [0, 0, 0],
                'end' => [8, 5, 2],
                'expected' => [
                    [0, 0, 0],
                    [1, 1, 1],
                    [2, 2, 2],
                    [3, 3, 2],
                    [4, 4, 2],
                    [5, 5, 2],
                    [6, 5, 2],
                    [7, 5, 2],
                    [8, 5, 2],
                ]
            ],
        ];
    }

    /**
     * Given start and dimension points
     * When getPlanarPoints is called with the provided points
     * Then a collection of points of a rectangular plane will be returned.
     *
     * @covers ::getPlanarPoints
     *
     * @param array $start
     * @param array $dimensions
     * @param array $expected
     * @param array|string[] $axis
     *
     * @dataProvider planarPointsProvider
     */
    final public function testGetPlanarPoints(array $start, array $dimensions, array $expected, array $axis = ['x', 'y']): void
    {
        $startPoint = new Vector(...$start);
        $dimensionPoint = new Vector(...$dimensions);
        $vectorsClass = $this->makeHasVectorsClass();
        $results = $vectorsClass->getPlanarPoints($startPoint, $dimensionPoint, ...$axis);

        self::assertCount(count($expected), $results);
        $resultsArray = $results->map(fn($result) => array_values($result->toArray()))->toArray();
        foreach ($expected as $point) {
            self::assertContains($point, $resultsArray);
        }
    }

    /**
     * Sample sets for getPlanarPoints
     */
    final public function planarPointsProvider(): array
    {
        return [
            'start and dimensions are equal return one point' => [
                'start' => [0, 0, 0],
                'dimensions' => [0, 0, 0],
                'expected' => [[0, 0, 0]]
            ],
            'start with 1x1 dimensions returns one point' => [
                'start' => [0, 0, 0],
                'dimensions' => [1, 1, 0],
                'expected' => [[0, 0, 0]]
            ],
            '2x2 dimensions returns four points' => [
                'start' => [0, 0, 0],
                'dimensions' => [2, 2, 0],
                'expected' => [
                    [0, 0, 0], [1, 0, 0],
                    [0, 1, 0], [1, 1, 0]
                ]
            ],
            '2x5 dimensions returns ten points' => [
                'start' => [0, 0, 0],
                'dimensions' => [2, 5, 0],
                'expected' => [
                    [0, 0, 0], [1, 0, 0],
                    [0, 1, 0], [1, 1, 0],
                    [0, 2, 0], [1, 2, 0],
                    [0, 3, 0], [1, 3, 0],
                    [0, 4, 0], [1, 4, 0],
                ]
            ],
            '5x2 dimensions returns ten points' => [
                'start' => [0, 0, 0],
                'dimensions' => [5, 2, 0],
                'expected' => [
                    [0, 0, 0], [1, 0, 0], [2, 0, 0], [3, 0, 0], [4, 0, 0],
                    [0, 1, 0], [1, 1, 0], [2, 1, 0], [3, 1, 0], [4, 1, 0],
                ]
            ],
            '3x3 dimensions on y and z returns nine points' => [
                'start' => [0, 0, 0],
                'dimensions' => [0, 3, 3],
                'expected' => [
                    [0, 0, 0], [0, 0, 1], [0, 0, 2],
                    [0, 1, 0], [0, 1, 1], [0, 1, 2],
                    [0, 2, 0], [0, 2, 1], [0, 2, 2],
                ],
                'axis' => ['y', 'z']
            ],
        ];
    }

    /**
     * Given a class with HasVectors
     * When getVectors is called
     * Then the anchor and dimensions will be used to get all planar points
     *
     * @covers ::getVectors
     */
    final public function testGetVectorsReturnsAllPointsWithinThisShape(): void
    {
        $vectorsClass = $this->makeHasVectorsClass(['x' => 5, 'y' => 7, 'width' => 2, 'height' => 2]);
        $results = $vectorsClass->getVectors();
        self::assertCount(4, $results);
        $resultsArray = $results->map(fn($result) => array_values($result->toArray()))->toArray();
        self::assertContains([5, 7, 0], $resultsArray);
        self::assertContains([6, 7, 0], $resultsArray);
        self::assertContains([5, 8, 0], $resultsArray);
        self::assertContains([6, 8, 0], $resultsArray);
    }

    /**
     * Create an instance of a class using HasVectors trait.
     *
     * @param array $attributes
     *
     * @return object
     */
    private function makeHasVectorsClass(array $attributes = []): object
    {
        return new class($attributes) {
            use HasVectors;

            final public function __construct(array $attributes = [])
            {
                foreach ($attributes as $name => $attribute) {
                    $this->{$name} = $attribute;
                }
            }

            final public function __get(string $name): mixed
            {
                return $this->{$name} ?? null;
            }

            final public function __set(string $name, mixed $attribute): void
            {
                $this->{$name} = $attribute;
            }

            final public function __isset(string $name): bool
            {
                return isset($this->{$name});
            }
        };
    }
}
