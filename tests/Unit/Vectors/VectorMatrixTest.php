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
        $vectors = new Collection([$one, $two, $three, $four]);
        $matrix->assignVectors($vectors);
        self::assertEquals($one->getBelongsTo(), $matrix->getVector(...$one->toArray())->getBelongsTo());
        self::assertEquals($two->getBelongsTo(), $matrix->getVector(...$two->toArray())->getBelongsTo());
        self::assertEquals($three->getBelongsTo(), $matrix->getVector(...$three->toArray())->getBelongsTo());
        self::assertEquals($four->getBelongsTo(), $matrix->getVector(...$four->toArray())->getBelongsTo());
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
        $matrix->assignVectors(new Collection([
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
        $badVectors = new Collection([
            new Vector(5, 1, 2),
            new Vector(2, -1, 0),
        ]);
        $assignVectors = new Collection([
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
     * @covers ::assignAvailablePosition
     */
    final public function testAssignAvailablePositionWhenTooBig(): void
    {
        $matrix = (new VectorMatrix(2, 2, 2))->create();
        $vectorModel = $this->makeVectorModelClass(['width' => 10, 'height' => 10]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is no available space for ' . get_class($vectorModel));
        $matrix->assignAvailablePosition($vectorModel);
    }

    /**
     * Given an incoming VectorModel
     * When the vector model dimensions do not fit available matrix space
     * Then and exception will be thrown.
     *
     * @covers ::assignAvailablePosition
     */
    final public function testAssignAvailablePositionWithNoSpace(): void
    {
        $matrix = (new VectorMatrix(2, 2, 1))->create();
        $matrix->assignVectors(new Collection([
            new Vector(1, 0, 0),
            new Vector(1, 1, 0),
        ]));
        $vectorModel = $this->makeVectorModelClass(['width' => 2, 'height' => 1]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is no available space to set anchor for ' . get_class($vectorModel));
        $matrix->assignAvailablePosition($vectorModel);
    }

    /**
     * Given an incoming VectorModel
     * When there is available space for the provided dimensions in the matrix
     * Then the anchor point will be assigned to the model, and matrix occupied
     *
     * @covers ::assignAvailablePosition
     *
     * @throws Exception
     */
    final public function testAssignAvailablePositionWithSpaceFound(): void
    {
        $matrix = (new VectorMatrix(2, 2, 1))->create();
        $matrix->assignVectors(new Collection([
            new Vector(0, 0, 0),
            new Vector(1, 0, 0),
        ]));
        self::assertCount(2, $matrix->getAvailableVectors());
        $vectorModel = $this->makeVectorModelClass(['width' => 2, 'height' => 1]);
        $matrix->assignAvailablePosition($vectorModel);
        self::assertEquals(['x' => 0, 'y' => 1, 'z' => 0], $vectorModel->getAnchorPoint()->toArray());
        self::assertCount(0, $matrix->getAvailableVectors());
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
}
