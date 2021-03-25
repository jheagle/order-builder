<?php

namespace Tests\Feature\Services\PrintSheetService;

use Tests\TestCase;
use App\Vectors\Vector;
use App\Vectors\VectorMatrix;
use App\Models\PrintSheetItem;
use Illuminate\Support\Collection;
use App\Services\PrintSheetService;

/**
 * Test placing of a variety of sized print sheet items onto a sheet
 *
 * @package Tests\Feature\Services\PrintSheetService
 *
 * @group Unit
 * @group Services
 * @group PrintSheetService
 * @group SortItems
 *
 * @coversDefaultClass PrintSheetService
 */
class SetPrintItemPositionsTest extends TestCase
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

        $matrix = (new VectorMatrix($this->service::SHEET_WIDTH, $this->service::SHEET_HEIGHT))->create();
        $sheetItemCollection = $this->service->sortPrintSheetItems($sheetItemCollection)->map(
            fn ($sheetItem) => $matrix->assignAvailablePosition($sheetItem)
        );

        self::assertCount($expectedVectors->count(), $sheetItems);
        self::assertTrue($sheetItemCollection->every(
            function ($sheetItem) use ($expectedVectors): bool {
                $sheetVector = $sheetItem->getAnchorPoint();
                return $expectedVectors->contains(fn (Vector $vector) => $vector->equals($sheetVector));
            }
        ));
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
                        'width' => 5,
                        'height' => 2
                    ]
                ],
                'expected' => [
                    [0, 0]
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
