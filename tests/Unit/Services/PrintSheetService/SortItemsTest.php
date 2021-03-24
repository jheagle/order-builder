<?php

namespace Tests\Unit\Services\PrintSheetService;

use Tests\TestCase;
use App\Vectors\Vector;
use App\Models\PrintSheetItem;
use Illuminate\Support\Collection;
use App\Services\PrintSheetService;

/**
 * Test placing of a variety of sized print sheet items onto a sheet
 *
 * @package Tests\Unit\Services\PrintSheetService
 *
 * @group Unit
 * @group Services
 * @group PrintSheetService
 * @group SortItems
 *
 * @coversDefaultClass PrintSheetService
 */
class SortItemsTest extends TestCase
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
     */
    final public function testPositionSet(array $sheetItems, array $expected): void
    {
        $sheetItemCollection = $this->makeSheetItems($sheetItems);
        $expectedVectors = $this->makeVectorCollection($expected);

        $sheetItemCollection = $this->service->sortPrintSheetItems($sheetItemCollection);

        foreach ($sheetItemCollection as $i => $sheetItem) {
            self::assertTrue($expectedVectors[$i]->equals($sheetItem->getDimensions()));
        }
    }

    /**
     * Test provider for testing positions
     *
     * @return int[]
     */
    final public function sheetItemProvider(): array
    {
        return [
            'single 5x2 should be top left' => [
                'sheetItems' => [
                    [
                        'width' => 1,
                        'height' => 1
                    ],
                    [
                        'width' => 2,
                        'height' => 2
                    ],
                    [
                        'width' => 2,
                        'height' => 5
                    ],
                    [
                        'width' => 3,
                        'height' => 3
                    ],
                    [
                        'width' => 5,
                        'height' => 2
                    ],
                    [
                        'width' => 4,
                        'height' => 4
                    ],
                ],
                'expected' => [
                    [5, 2, 1],
                    [2, 5, 1],
                    [4, 4, 1],
                    [3, 3, 1],
                    [2, 2, 1],
                    [1, 1, 1]
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
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height']
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
