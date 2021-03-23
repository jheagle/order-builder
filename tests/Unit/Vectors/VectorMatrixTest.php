<?php

namespace Tests\Unit\Vectors;

use App\Vectors\Vector;
use App\Vectors\VectorMatrix;
use Illuminate\Support\Collection;
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
    public function testCreateVectorMatrix()
    {
        $matrix = new VectorMatrix();
        $this->assertEmpty($matrix->getMatrixVectors());
        $matrix->width = 10;
        $matrix->height = 15;
        $matrix->depth = 5;
        $matrix->create();
        $this->assertCount(750, $this->testGetMatrixVectors());
    }

    /**
     * Given a VectorMatrix
     * When assigning a Vector and the Matrix is empty
     * Then an out of bounds exception will be thrown
     *
     * @covers ::assignVector
     */
    public function testAssignVectorWithEmptyMatrixThrowsException()
    {
        $matrix = new VectorMatrix();
        $vector = new Vector(0, 0, 0);
        $this->expectException(\OutOfBoundsException::class);
        $matrix->assignVector($vector);
    }

    /**
     * Given a VectorMatrix
     * When assigning a Vector out of the Matrix limits
     * Then an out of bounds exception will be thrown
     *
     * @covers ::assignVector
     */
    public function testAssignVectorWithInvalidPositionThrowsException()
    {
        $matrix = (new VectorMatrix())->create();
        $vector = new Vector(0, 1, 0);
        $this->expectException(\OutOfBoundsException::class);
        $matrix->assignVector($vector);
    }

    /**
     * Given a VectorMatrix
     * When assigning a Vector within the Matrix
     * Then a new Vector will be assigned to the Matrix
     *
     * @covers ::assignVector
     */
    public function testAssignVectorWithinMatrixWillApplyNewVector()
    {
        $matrix = (new VectorMatrix(2, 2, 1))->create();
        $assignedValue = 'assigned value';
        $x = 0;
        $y = 0;
        $z = 0;
        $vector = new Vector($x, $y, $z, $assignedValue);
        $matrix->assignVector($vector);
        $this->assertEquals($assignedValue, $matrix->getVector($x, $y, $z)->getBelongsTo());
    }

    /**
     * Given a collection of Vectors
     * When passing this collection to assignVectors
     * Then each Vector will be assigned
     *
     * @covers ::assignVectors
     */
    public function testAssignVectors()
    {
        $matrix = (new VectorMatrix(11, 16, 10))->create();
        $one = new Vector(0, 0, 0, 'assigned value one');
        $two = new Vector(3, 1, 6, 'assigned value two');
        $three = new Vector(7, 15, 3, 'assigned value three');
        $four = new Vector(10, 5, 9, 'assigned value four');
        $vectors = new Collection([$one, $two, $three, $four]);
        $matrix->assignVectors($vectors);
        $this->assertEquals($one->getBelongsTo(), $matrix->getVector(...$one->toArray())->getBelongsTo());
        $this->assertEquals($two->getBelongsTo(), $matrix->getVector(...$two->toArray())->getBelongsTo());
        $this->assertEquals($three->getBelongsTo(), $matrix->getVector(...$three->toArray())->getBelongsTo());
        $this->assertEquals($four->getBelongsTo(), $matrix->getVector(...$four->toArray())->getBelongsTo());
    }

    /**
     * Given
     * When
     * Then
     *
     * @covers ::canUseVectors
     */
    public function testCanUseVectors()
    {
        $this->markTestIncomplete(
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
    public function testGetAvailableVectors()
    {
        $this->markTestIncomplete(
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
    public function testGetMatrixVectors()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
