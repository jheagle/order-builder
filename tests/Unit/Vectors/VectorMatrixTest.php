<?php

namespace Tests\Unit\Vectors;

use App\Vectors\Vector;
use App\Vectors\VectorMatrix;
use App\Vectors\VectorModel;
use Exception;
use Illuminate\Support\Collection;
use OutOfBoundsException;
use Tests\TestCase;

/**
 * Test the VectorMatrix class
 *
 * @package Tests\Unit\Vectors
 *
 * @group Unit
 * @group Vectors
 * @group VectorMatrix
 *
 * @coversDefaultClass VectorMatrix
 */
class VectorMatrixTest extends TestCase
{
    /**
     * Given a new VectorMatrix instance
     * When no arguments are provided
     * Then calling create will create a 1x1x1 or use set width / height / depth
     *
     * @covers ::create
     */
    final public function testCreateVectorMatrix(): void
    {
        $matrix = new VectorMatrix();
        self::assertEmpty($matrix->getMatrixVectors());
        $matrix->width = 10;
        $matrix->height = 15;
        $matrix->depth = 5;
        $matrix->create();
        self::assertCount(750, $matrix->getMatrixVectors());
    }

    /**
     * Given a VectorMatrix
     * When assigning a Vector and the Matrix is empty
     * Then an out of bounds exception will be thrown
     *
     * @covers ::assignVector
     */
    final public function testAssignVectorWithEmptyMatrixThrowsException(): void
    {
        $matrix = new VectorMatrix();
        $vector = new Vector(0, 0, 0);
        $this->expectException(OutOfBoundsException::class);
        $matrix->assignVector($vector);
    }

    /**
     * Given a VectorMatrix
     * When assigning a Vector out of the Matrix limits
     * Then an out of bounds exception will be thrown
     *
     * @covers ::assignVector
     */
    final public function testAssignVectorWithInvalidPositionThrowsException(): void
    {
        $matrix = (new VectorMatrix())->create();
        $vector = new Vector(0, 1, 0);
        $this->expectException(OutOfBoundsException::class);
        $matrix->assignVector($vector);
    }

    /**
     * Given a VectorMatrix
     * When assigning a Vector within the Matrix
     * Then a new Vector will be assigned to the Matrix
     *
     * @covers ::assignVector
     */
    final public function testAssignVectorWithinMatrixWillApplyNewVector(): void
    {
        $matrix = (new VectorMatrix(2, 2, 1))->create();
        $assignedValue = 'assigned value';
        $x = 0;
        $y = 0;
        $z = 0;
        $vector = new Vector($x, $y, $z, $assignedValue);
        $matrix->assignVector($vector);
        self::assertEquals($assignedValue, $matrix->getVector($x, $y, $z)->getBelongsTo());
    }

    /**
     * Given a collection of Vectors
     * When passing this collection to assignVectors
     * Then each Vector will be assigned
     *
     * @covers ::assignVectors
     */
    final public function testAssignVectors(): void
    {
        $matrix = (new VectorMatrix(11, 16, 10))->create();
        $one = new Vector(0, 0, 0, 'assigned value one');
        $two = new Vector(3, 1, 6, 'assigned value two');
        $three = new Vector(7, 15, 3, 'assigned value three');
        $four = new Vector(10, 5, 9, 'assigned value four');
        $vectors = Collection::make([$one, $two, $three, $four]);
        $matrix->assignVectors($vectors);
        self::assertEquals($one->getBelongsTo(), $matrix->getVector(...$one->toArray())->getBelongsTo());
        self::assertEquals($two->getBelongsTo(), $matrix->getVector(...$two->toArray())->getBelongsTo());
        self::assertEquals($three->getBelongsTo(), $matrix->getVector(...$three->toArray())->getBelongsTo());
        self::assertEquals($four->getBelongsTo(), $matrix->getVector(...$four->toArray())->getBelongsTo());
    }

    /**
     * Given a matrix and a start position
     * When searching contiguous with no condition provided
     * Then all matrix vectors are contiguous towards positive axis
     *
     * @covers ::findAllContiguousPoints
     *
     * @throws Exception
     */
    final public function testFindAllContiguousPointsWithAllTrue(): void
    {
        $matrix = (new VectorMatrix(3, 3, 3))
            ->create();
        $zerosVector = new Vector(0, 0, 0);
        self::assertCount(27, $matrix->findAllContiguousPoints($zerosVector));
        $onesVector = new Vector(1, 1, 1);
        self::assertCount(8, $matrix->findAllContiguousPoints($onesVector));
        $twosVector = new Vector(2, 2, 2);
        self::assertCount(1, $matrix->findAllContiguousPoints($twosVector));
    }

    /**
     * Given a matrix and a start position
     * When searching contiguous with a condition provided
     * Then only vectors not matching the condition will be considered contiguous
     *
     * @covers ::findAllContiguousPoints
     *
     * @throws Exception
     */
    final public function testFindAllContiguousPointsWithSomeFalse(): void
    {
        $matrix = (new VectorMatrix(3, 3, 3))
            ->create()
            ->assignVectors(Collection::make([
                new Vector(0, 0, 1, null),
                new Vector(1, 2, 1, null),
                new Vector(2, 2, 2, null),
            ]));
        $testFunction = fn (Vector $vector) => $vector->getBelongsTo() !== null;
        $zerosVector = new Vector(0, 0, 0);
        self::assertCount(19, $matrix->findAllContiguousPoints($zerosVector, $testFunction));
        $onesVector = new Vector(1, 1, 1);
        self::assertCount(5, $matrix->findAllContiguousPoints($onesVector, $testFunction));
        $twosVector = new Vector(2, 2, 2);
        self::assertCount(0, $matrix->findAllContiguousPoints($twosVector, $testFunction));
    }

    /**
     * Given a matrix of 3x3x3
     * When assigning a vector model of 2x2x2
     * Then the one vector model will be found as an occupier
     *
     * @covers ::getDistinctOccupiers
     *
     * @throws Exception
     */
    final public function testRetrieveAllOccupyingVectors(): void
    {
        $matrix = (new VectorMatrix(3, 3, 3))
            ->create()
            ->assignAvailablePositions(Collection::make([
                $this->makeVectorModelClass(['width' => 2, 'height' => 2, 'depth' => 2])
            ]));
        self::assertCount(1, $matrix->getDistinctOccupiers());
    }

    /**
     * Given a matrix with vectors
     * When calling getMatrixVectors on the matrix
     * Then a collection of all the vectors contained in the matrix will be returned.
     *
     * @covers ::getMatrixVectors
     */
    final public function testGetMatrixVectorsAllVectorsContainedInMatrix(): void
    {
        $matrix = (new VectorMatrix(2, 2, 2))->create();
        self::assertCount(8, $matrix->getMatrixVectors());
    }

    /**
     * Given a matrix with vectors
     * When some of the vectors are occupied, and calling getAvailableVectors
     * Then a collection of the unoccupied vectors will be returned
     *
     * @covers ::getAvailableVectors
     */
    final public function testGetAvailableVectors(): void
    {
        $matrix = (new VectorMatrix(3, 3, 3))->create();
        self::assertCount(27, $matrix->getAvailableVectors());
        $matrix->assignVectors(Collection::make([
            new Vector(0, 1, 2),
            new Vector(2, 1, 0),
        ]));
        self::assertCount(25, $matrix->getAvailableVectors());
    }

    /**
     * Given a set of vectors and a matrix with vectors
     * When those vectors exist or do not exits in the matrix
     * Then canUseVectors will will return true for all existing, otherwise false when any nonexistent
     *
     * @covers ::canUseVectors
     */
    final public function testCanUseVectors(): void
    {
        $badVectors = Collection::make([
            new Vector(5, 1, 2),
            new Vector(2, -1, 0),
        ]);
        $assignVectors = Collection::make([
            new Vector(0, 1, 2),
            new Vector(2, 1, 0),
        ]);
        $matrix = (new VectorMatrix(3, 3, 3))->create();
        self::assertCount(27, $matrix->getAvailableVectors());
        self::assertFalse($matrix->canUseVectors($badVectors));
        self::assertTrue($matrix->canUseVectors($assignVectors));
        $matrix->assignVectors($assignVectors);
        self::assertFalse($matrix->canUseVectors($assignVectors));
    }

    /**
     * Given an incoming VectorModel
     * When the vector model size is larger than the matrix available spaces
     * Then and exception will be thrown.
     *
     * @covers ::assignAvailablePositions
     */
    final public function testAssignAvailablePositionsWhenTooBig(): void
    {
        $matrix = (new VectorMatrix(2, 2, 2))->create();
        $vectorModel = $this->makeVectorModelClass(['width' => 10, 'height' => 10]);
        $this->expectException(Exception::class);
        $matrix->assignAvailablePositions(Collection::make([$vectorModel]));
    }

    /**
     * Given an incoming VectorModel
     * When the vector model dimensions do not fit available matrix space
     * Then and exception will be thrown.
     *
     * @covers ::assignAvailablePositions
     */
    final public function testAssignAvailablePositionWithNoSpace(): void
    {
        $matrix = (new VectorMatrix(2, 2, 1))->create();
        $matrix->assignVectors(Collection::make([
            new Vector(1, 0, 0),
            new Vector(1, 1, 0),
        ]));
        $vectorModel = $this->makeVectorModelClass(['width' => 2, 'height' => 1]);
        $this->expectException(Exception::class);
        $matrix->assignAvailablePositions(Collection::make([$vectorModel]));
    }

    /**
     * Given an incoming VectorModel
     * When there is available space for the provided dimensions in the matrix
     * Then the anchor point will be assigned to the model, and matrix occupied
     *
     * @covers ::assignAvailablePositions
     *
     * @throws Exception
     */
    final public function testAssignAvailablePositionsWithSpaceFound(): void
    {
        $matrix = (new VectorMatrix(2, 2, 1))->create();
        $matrix->assignVectors(Collection::make([
            new Vector(0, 0, 0),
            new Vector(1, 0, 0),
        ]));
        self::assertCount(2, $matrix->getAvailableVectors());
        $vectorModel = $this->makeVectorModelClass(['width' => 2, 'height' => 1]);
        $matrix->assignAvailablePositions(Collection::make([$vectorModel]));
        self::assertEquals(['x' => 0, 'y' => 1, 'z' => 0], $vectorModel->getAnchorPoint()->toArray());
        self::assertCount(0, $matrix->getAvailableVectors());
    }

    /**
     * Given a matrix and a start position
     * When searching contiguous with a condition provided
     * Then only vectors not matching the condition will be considered contiguous
     *
     * @covers ::findAllContiguousPoints
     *
     * @throws Exception
     */
    final public function testGetContiguousAvailablePointsAllTogether(): void
    {
        $matrix = (new VectorMatrix(3, 3, 3))
            ->create()
            ->assignVectors(Collection::make([
                new Vector(0, 0, 1, null),
                new Vector(1, 2, 1, null),
                new Vector(2, 2, 2, null),
            ]));
        $results = $matrix->getContiguousAvailableVectors();
        self::assertCount(3, $results);
        self::assertCount(19, $results->get(0));
        self::assertCount(10, $results->get(1));
        self::assertCount(8, $results->get(2));
    }

    /**
     * Given a matrix with occupied vectors
     * When searching available contiguous vectors
     * Then two groups of contiguous vectors will be found
     *
     * @covers ::findAllContiguousPoints
     *
     * @throws Exception
     */
    final public function testGetContiguousAvailablePointsSeparateGroups(): void
    {
        $matrix = (new VectorMatrix(3, 3, 3))
            ->create()
            ->assignVectors(Collection::make([
                new Vector(0, 0, 1, null),
                new Vector(1, 0, 1, null),
                new Vector(2, 0, 1, null),
                new Vector(0, 1, 1, null),
                new Vector(1, 1, 1, null),
                new Vector(2, 1, 1, null),
                new Vector(0, 2, 1, null),
                new Vector(1, 2, 1, null),
                new Vector(2, 2, 1, null),
            ]));
        $results = $matrix->getContiguousAvailableVectors();
        self::assertCount(2, $results);
        self::assertCount(9, $results->get(0));
        self::assertCount(9, $results->get(1));
    }

    /**
     * Test various collections of vectors fit as expected.
     *
     * @dataProvider vectorModelProvider
     *
     * @param array $vectorItems
     * @param array $expected
     *
     * @covers ::assignAvailablePositions
     *
     * @throws Exception
     */
    final public function testPositionSet(array $vectorItems, array $expected): void
    {
        $expectedVectors = $this->makeVectorCollection($expected);
        $vectorItemCollection = $this->makeVectorItems($vectorItems);

        (new VectorMatrix(10, 15))
            ->create()
            ->assignAvailablePositions($vectorItemCollection);

        self::assertCount($expectedVectors->count(), $vectorItemCollection);
        self::assertTrue(
            $vectorItemCollection->every(
                fn(VectorModel $sheetItem) => $expectedVectors->contains(
                    fn(Vector $vector) => $vector->equals($sheetItem->getAnchorPoint())
                )
            )
        );
    }

    /**
     * Test provider for testing positions
     *
     * @return int[]
     */
    final public function vectorModelProvider(): array
    {
        return [
            'single 5x2 should be top left' => [
                'vectorItems' => [
                    [5, 2]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 2x5 should be top left' => [
                'vectorItems' => [
                    [2, 5]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 4x4 should be top left' => [
                'vectorItems' => [
                    [4, 4]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 3x3 should be top left' => [
                'vectorItems' => [
                    [3, 3]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 2x2 should be top left' => [
                'vectorItems' => [
                    [2, 2]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 1x1 should be top left' => [
                'vectorItems' => [
                    [1, 1]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'use one of each to fill the page' => [
                'vectorItems' => [
                    [5, 2],
                    [2, 5],
                    [4, 4],
                    [3, 3],
                    [2, 2],
                    [1, 1],
                ],
                'expected' => [
                    [0, 0],
                    [5, 0],
                    [0, 2],
                    [7, 0],
                    [7, 3],
                    [4, 2],
                ]
            ],
            'use two of each to fill the page' => [
                'vectorItems' => [
                    [5, 2], [5, 2],
                    [2, 5], [2, 5],
                    [4, 4], [4, 4],
                    [3, 3], [3, 3],
                    [2, 2], [2, 2],
                    [1, 1], [1, 1],
                ],
                'expected' => [
                    [0, 0], [5, 0],
                    [0, 2], [2, 2],
                    [4, 2], [4, 6],
                    [0, 7], [0, 10],
                    [8, 2], [8, 4],
                    [8, 6], [9, 6],
                ]
            ],
            'use three of each to fill the page' => [
                'vectorItems' => [
                    [5, 2], [5, 2], [5, 2],
                    [2, 5], [2, 5], [2, 5],
                    [4, 4], [4, 4], [4, 4],
                    [3, 3], [3, 3], [3, 3],
                    [2, 2], // [2, 2], [2, 2],
                    [1, 1], [1, 1], [1, 1],
                ],
                'expected' => [
                    [0, 0], [5, 0], [0, 2],
                    [5, 2], [7, 2], [0, 4],
                    [2, 7], [6, 7], [0, 11],
                    [2, 4], [4, 11], [7, 11],
                    [0, 9], // There are two [2, 2] that cannot fit, unused area of 8 units
                    [9, 2], [9, 3], [9, 4],
                ]
            ],
            'perfect fit of 2x5 should fill the entire matrix' => [
                'vectorItems' => [
                    [2, 5], [2, 5], [2, 5], [2, 5], [2, 5],
                    [2, 5], [2, 5], [2, 5], [2, 5], [2, 5],
                    [2, 5], [2, 5], [2, 5], [2, 5], [2, 5],
                ],
                'expected' => [
                    [0, 0], [2, 0], [4, 0], [6, 0], [8, 0],
                    [0, 5], [2, 5], [4, 5], [6, 5], [8, 5],
                    [0, 10], [2, 10], [4, 10], [6, 10], [8, 10],
                ]
            ],
            'max 5x2 fit in matrix' => [
                'vectorItems' => [
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                ],
                'expected' => [
                    [0, 0], [5, 0],
                    [0, 2], [5, 2],
                    [0, 4], [5, 4],
                    [0, 6], [5, 6],
                    [0, 8], [5, 8],
                    [0, 10], [5, 10],
                    [0, 12], [5, 12],
                ]
            ],
            'max 4x4 fit in matrix' => [
                'vectorItems' => [
                    [4, 4], [4, 4],
                    [4, 4], [4, 4],
                    [4, 4], [4, 4],
                ],
                'expected' => [
                    [0, 0], [4, 0],
                    [0, 4], [4, 4],
                    [0, 8], [4, 8],
                ]
            ],
            'max 3x3 fit in matrix' => [
                'vectorItems' => [
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                ],
                'expected' => [
                    [0, 0], [3, 0], [6, 0],
                    [0, 3], [3, 3], [6, 3],
                    [0, 6], [3, 6], [6, 6],
                    [0, 9], [3, 9], [6, 9],
                    [0, 12], [3, 12], [6, 12],
                ]
            ],
            'max 2x2 fit in matrix' => [
                'vectorItems' => [
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                ],
                'expected' => [
                    [0, 0], [2, 0], [4, 0], [6, 0], [8, 0],
                    [0, 2], [2, 2], [4, 2], [6, 2], [8, 2],
                    [0, 4], [2, 4], [4, 4], [6, 4], [8, 4],
                    [0, 6], [2, 6], [4, 6], [6, 6], [8, 6],
                    [0, 8], [2, 8], [4, 8], [6, 8], [8, 8],
                    [0, 10], [2, 10], [4, 10], [6, 10], [8, 10],
                    [0, 12], [2, 12], [4, 12], [6, 12], [8, 12],
                ]
            ],
            'max 1x1 fit in matrix' => [
                'vectorItems' => [
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                ],
                'expected' => [
                    [0, 0], [1, 0], [2, 0], [3, 0], [4, 0], [5, 0], [6, 0], [7, 0], [8, 0], [9, 0],
                    [0, 1], [1, 1], [2, 1], [3, 1], [4, 1], [5, 1], [6, 1], [7, 1], [8, 1], [9, 1],
                    [0, 2], [1, 2], [2, 2], [3, 2], [4, 2], [5, 2], [6, 2], [7, 2], [8, 2], [9, 2],
                    [0, 3], [1, 3], [2, 3], [3, 3], [4, 3], [5, 3], [6, 3], [7, 3], [8, 3], [9, 3],
                    [0, 4], [1, 4], [2, 4], [3, 4], [4, 4], [5, 4], [6, 4], [7, 4], [8, 4], [9, 4],
                    [0, 5], [1, 5], [2, 5], [3, 5], [4, 5], [5, 5], [6, 5], [7, 5], [8, 5], [9, 5],
                    [0, 6], [1, 6], [2, 6], [3, 6], [4, 6], [5, 6], [6, 6], [7, 6], [8, 6], [9, 6],
                    [0, 7], [1, 7], [2, 7], [3, 7], [4, 7], [5, 7], [6, 7], [7, 7], [8, 7], [9, 7],
                    [0, 8], [1, 8], [2, 8], [3, 8], [4, 8], [5, 8], [6, 8], [7, 8], [8, 8], [9, 8],
                    [0, 9], [1, 9], [2, 9], [3, 9], [4, 9], [5, 9], [6, 9], [7, 9], [8, 9], [9, 9],
                    [0, 10], [1, 10], [2, 10], [3, 10], [4, 10], [5, 10], [6, 10], [7, 10], [8, 10], [9, 10],
                    [0, 11], [1, 11], [2, 11], [3, 11], [4, 11], [5, 11], [6, 11], [7, 11], [8, 11], [9, 11],
                    [0, 12], [1, 12], [2, 12], [3, 12], [4, 12], [5, 12], [6, 12], [7, 12], [8, 12], [9, 12],
                    [0, 13], [1, 13], [2, 13], [3, 13], [4, 13], [5, 13], [6, 13], [7, 13], [8, 13], [9, 13],
                    [0, 14], [1, 14], [2, 14], [3, 14], [4, 14], [5, 14], [6, 14], [7, 14], [8, 14], [9, 14],
                ]
            ],
        ];
    }

    /**
     * Create an instance of a VectorModel subclass.
     *
     * @param array $attributes
     *
     * @return VectorModel
     */
    private function makeVectorModelClass(array $attributes = []): VectorModel
    {
        return new class($attributes) extends VectorModel {

            final public function __construct(array $attributes = [])
            {
                parent::__construct();
                foreach ($attributes as $name => $attribute) {
                    $this->{$name} = $attribute;
                }
            }
        };
    }

    /**
     * Helper for converting array to Sheet Items
     *
     * @param array $itemDimensions
     *
     * @return Collection
     */
    private function makeVectorItems(array $itemDimensions): Collection
    {
        return Collection::make(array_map(
            function (array $dimensions): VectorModel {
                return new class(...$dimensions) extends VectorModel {
                    public int $x = 0;
                    public int $y = 0;
                    public int $width = 0;
                    public int $height = 0;

                    public function __construct(int $width = 0, int $height = 0)
                    {
                        parent::__construct();
                        $this->width = $width;
                        $this->height = $height;
                    }
                };
            },
            $itemDimensions
        ));
    }

    /**
     * Helper for converting arrays to Vectors
     *
     * @param array $vectorCoordinates
     *
     * @return Collection
     */
    private function makeVectorCollection(array $vectorCoordinates): Collection
    {
        return Collection::make(
            array_map(static function (array $coordinates): Vector {
                return new Vector(...$coordinates);
            }, $vectorCoordinates)
        );
    }
}
