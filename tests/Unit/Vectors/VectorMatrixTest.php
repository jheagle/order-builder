<?php

namespace Tests\Unit\Vectors;

use App\Vectors\Vector;
use App\Vectors\VectorMatrix;
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
        self::assertCount(750,$matrix->getMatrixVectors());
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
        $vectors = new Collection([$one, $two, $three, $four]);
        $matrix->assignVectors($vectors);
        self::assertEquals($one->getBelongsTo(), $matrix->getVector(...$one->toArray())->getBelongsTo());
        self::assertEquals($two->getBelongsTo(), $matrix->getVector(...$two->toArray())->getBelongsTo());
        self::assertEquals($three->getBelongsTo(), $matrix->getVector(...$three->toArray())->getBelongsTo());
        self::assertEquals($four->getBelongsTo(), $matrix->getVector(...$four->toArray())->getBelongsTo());
    }

    /**
     * Given
     * When
     * Then
     *
     * @covers ::canUseVectors
     */
    final public function testCanUseVectors(): void
    {
        self::markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Given
     * When
     * Then
     *
     * @covers ::getAvailableVectors
     */
    final public function testGetAvailableVectors(): void
    {
        self::markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Given
     * When
     * Then
     *
     * @covers ::getMatrixVectors
     */
    final public function testGetMatrixVectors(): void
    {
        self::markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
