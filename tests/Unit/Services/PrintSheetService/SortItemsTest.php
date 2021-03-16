<?php

namespace Tests\Unit\Services\PrintSheetService;

use Tests\TestCase;
use App\Vectors\Vector;
use App\Vectors\VectorMatrix;
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
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PrintSheetService;
    }

    /**
     *
     *
     * @dataProvider sheetItemProvider
     */
    public function testPositionSet(array $sheetItems, array $expected): void
    {
        $sheetItemCollection = $this->makeSheetItems($sheetItems);
        $expectedVectors = $this->makeVectorCollection($expected);

        $matrix = new VectorMatrix($this->service::SHEET_WIDTH, $this->service::SHEET_HEIGHT);
        $sheetItemCollection = $this->service->sortPrintSheetItems($sheetItemCollection)->map(
            fn ($sheetItem) => $this->service->assignAvailablePosition($sheetItem, $matrix)
        );

        $this->assertCount($expectedVectors->count(), $sheetItems);
        $this->assertTrue($sheetItemCollection->every(
            function ($sheetItem) use ($expectedVectors): bool {
                $sheetVector = $sheetItem->getAnchorPoint();
                return $expectedVectors->contains(fn (Vector $vector) => $vector->equals($sheetVector));
            }
        ));
    }

    public function sheetItemProvider(): array
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

    private function makeSheetItems(array $itemDimensions): Collection
    {
        return new Collection(
            array_map(function (array $dimensions): PrintSheetItem {
                return PrintSheetItem::factory([
                    'x_pos' => 0,
                    'y_pos' => 0,
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height']
                ])->unit()->make();
            }, $itemDimensions)
        );
    }

    private function makeVectorCollection(array $vectorCoordinates): Collection
    {
        return new Collection(
            array_map(function (array $coordinates): Vector {
                return new Vector(...$coordinates);
            }, $vectorCoordinates)
        );
    }
}
