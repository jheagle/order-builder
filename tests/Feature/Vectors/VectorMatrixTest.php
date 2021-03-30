<?php

namespace Tests\Feature\Vectors;

use JetBrains\PhpStorm\ArrayShape;
use Tests\TestCase;
use App\Vectors\Vector;
use App\Vectors\VectorMatrix;
use App\Models\PrintSheetItem;
use Illuminate\Support\Collection;
use App\Services\PrintSheetService;

/**
 * Test placing of a variety of sized print sheet items onto a sheet
 *
 * @package Tests\Feature\Vectors
 *
 * @group Feature
 * @group VectorMatrix
 * @group AssignPosition
 *
 * @coversDefaultClass VectorMatrix
 */
class VectorMatrixTest extends TestCase
{
    public PrintSheetService $service;

    /**
     * Set up these tests.
     *
     * @return void
     */
    final public function setUp(): void
    {
        parent::setUp();

        $this->service = new PrintSheetService;
    }

    /**
     * Test
     *
     * @dataProvider sheetItemProvider
     *
     * @param array $sheetItems
     * @param array $expected
     *
     * @covers ::assignAvailablePosition
     */
    final public function testPositionSet(array $sheetItems, array $expected): void
    {
        $expectedVectors = $this->makeVectorCollection($expected);

        $matrix = (new VectorMatrix($this->service::SHEET_WIDTH, $this->service::SHEET_HEIGHT))->create();
        $sheetItemCollection = $this->service->sortPrintSheetItems($this->makeSheetItems($sheetItems))->map(
            fn(PrintSheetItem $sheetItem) => $matrix
                ->assignAvailablePosition($sheetItem)
                ->getVector(...$sheetItem->getAnchorPoint()->toArray())
                ->getBelongsTo()
        );

        self::assertCount($expectedVectors->count(), $sheetItemCollection);
        self::assertTrue(
            $sheetItemCollection->every(
                fn(PrintSheetItem $sheetItem) => $expectedVectors->contains(
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
    #[ArrayShape(['single 5x2 should be top left' => "\int[][][]", 'fit a variety of each product length to fill all space' => "\int[][][]", 'perfect fit of 2x5 should fill the entire matrix' => "\int[][][]", 'max 5x2 fit in matrix' => "\int[][][]", 'max 4x4 fit in matrix' => "\int[][][]", 'max 3x3 fit in matrix' => "\int[][][]", 'max 2x2 fit in matrix' => "\int[][][]", 'max 1x1 fit in matrix' => "\int[][][]"])]
    final public function sheetItemProvider(): array
    {
        return [
            'single 5x2 should be top left' => [
                'sheetItems' => [
                    [5, 2]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'fit a variety of each product length to fill all space' => [
                'sheetItems' => [
                    [5, 2, 1], [5, 2, 1],
                    [5, 2, 1], [5, 2, 1],
                    [5, 2, 1], [2, 5, 1], [2, 5, 1], [1, 1, 1],
                    [1, 1, 1],
                    [4, 4, 1], [1, 1, 1], [1, 1, 1],
                    [1, 1, 1], [1, 1, 1],
                    [1, 1, 1], [1, 1, 1],
                    [4, 4, 1], [2, 2, 1],
                    [3, 3, 1], [1, 1, 1],
                    [1, 1, 1], [2, 2, 1],
                    [1, 1, 1],
                    [2, 2, 1], [2, 2, 1], [2, 2, 1], [2, 2, 1], [2, 2, 1],
                ],
                'expected' => [
                    [0, 0], [5, 0],
                    [0, 2], [5, 2],
                    [0, 4], [5, 4], [7, 4], [9, 4],
                    [9, 5],
                    [0, 6], [4, 6], [9, 6],
                    [4, 7], [9, 7],
                    [4, 8], [9, 8],
                    [4, 9], [8, 9],
                    [0, 10], [3, 10],
                    [3, 11], [8, 11],
                    [3, 12],
                    [0, 13], [2, 13], [4, 13], [6, 13], [8, 13],
                ]
            ],
            'perfect fit of 2x5 should fill the entire matrix' => [
                'sheetItems' => [
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
                'sheetItems' => [
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
                'sheetItems' => [
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
                'sheetItems' => [
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
                'sheetItems' => [
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
                'sheetItems' => [
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
     * Helper for converting array to Sheet Items
     *
     * @param array $itemDimensions
     *
     * @return Collection
     */
    private function makeSheetItems(array $itemDimensions): Collection
    {
        return new Collection(
            array_map(static function (array $dimensions): PrintSheetItem {
                return PrintSheetItem::factory([
                    'x_pos' => 0,
                    'y_pos' => 0,
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                    'size' => "{$dimensions[0]}x{$dimensions[1]}"
                ])->unit()->make();
            }, $itemDimensions)
        );
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
        return new Collection(
            array_map(static function (array $coordinates): Vector {
                return new Vector(...$coordinates);
            }, $vectorCoordinates)
        );
    }
}
