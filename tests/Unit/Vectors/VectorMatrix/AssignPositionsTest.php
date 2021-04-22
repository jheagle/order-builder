<?php

namespace Tests\Unit\Vectors\VectorMatrix;

use App\Vectors\VectorModel;
use Exception;
use Tests\TestCase;
use App\Vectors\VectorMatrix;
use Illuminate\Support\Collection;

/**
 * Test placing of a variety of sized print sheet items onto a sheet
 *
 * @package Tests\Unit\Vectors\VectorMatrix
 *
 * @group Unit
 * @group VectorMatrix
 * @group AssignPosition
 *
 * @coversDefaultClass VectorMatrix
 */
class AssignPositionsTest extends TestCase
{
    private const WIDTH = 10;
    private const HEIGHT = 15;
    private const DEPTH = 1;
    private const PRODUCTS = [
        ['width' => 1, 'height' => 1, 'depth' => 1],
        ['width' => 2, 'height' => 2, 'depth' => 1],
        ['width' => 3, 'height' => 3, 'depth' => 1],
        ['width' => 4, 'height' => 4, 'depth' => 1],
        ['width' => 5, 'height' => 2, 'depth' => 1],
        ['width' => 2, 'height' => 5, 'depth' => 1],
    ];

    /**
     * @dataProvider randomVectorModelProvider
     *
     * @param Collection $vectorModels
     *
     * @covers ::assignAvailablePositions
     *
     * @throws Exception
     */
    final public function testRandomCollectionsOfVectorModelsInMatrix(Collection $vectorModels): void
    {
        $modelsCount = $vectorModels->count();
        $matrix = (new VectorMatrix(10, 15))->create();
        try {
            $matrix->assignAvailablePositions($vectorModels);
            self::assertCount($modelsCount, $matrix->getDistinctOccupiers());
        } catch (Exception) {
            $this->assertLessThan($modelsCount, $matrix->getDistinctOccupiers()->count());
        }
    }

    /**
     * Build multiple tests for a variety of vector model collections
     *
     * @return array
     *
     * @throws Exception
     */
    final public function randomVectorModelProvider(): array
    {
        $maxSpace = self::WIDTH * self::HEIGHT;
        $testNumber = 50;
        $testIncrement = floor($maxSpace / $testNumber);
        $freeSpace = 0;
        $tests = [];
        while ($freeSpace < $maxSpace) {
            $vectors = $this->getVectorModelCollection($maxSpace, $freeSpace);
            $tests[sprintf(
                '%d/%d: Assign %d VectorModels with %d space remaining',
                count($tests) + 1,
                $testNumber,
                $vectors->count(),
                $freeSpace
            )] = ['vectorModels' => $vectors];
            $freeSpace += $testIncrement;
        }
        return $tests;
    }

    /**
     * Retrieve a collection of vector models
     *
     * @param int $maxSpace
     * @param int $freeSpace
     *
     * @return Collection
     *
     * @throws Exception
     */
    private function getVectorModelCollection(int $maxSpace = 150, int $freeSpace = 0): Collection
    {
        $spaceToFill = $maxSpace - $freeSpace;
        $vectors = Collection::make();
        do {
            $newVector = $this->getVectorModel($spaceToFill);
            if (!is_null($newVector)) {
                $dimensions = $newVector->getDimensions();
                $area = $dimensions->x * $dimensions->y * $dimensions->z;
                $spaceToFill -= $area;
                $vectors->push($newVector);
            }
        } while ($newVector !== null);
        return $vectors;
    }

    /**
     * Retrieve a single vector model with dimensions
     *
     * @param int $spaceRemaining
     *
     * @return VectorModel|null
     *
     * @throws Exception
     */
    private function getVectorModel(int $spaceRemaining = 150): ?VectorModel
    {
        $sizes = array_values(
            array_filter(
                self::PRODUCTS,
                fn($product) => $this->filterProductFit($product, $spaceRemaining)
            )
        );
        if (!$sizes) {
            return null;
        }
        return new class($sizes[random_int(0, count($sizes) - 1)]) extends VectorModel {
            public int $height = 0;
            public int $width = 0;

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
