<?php

namespace Tests\Unit\Services;

use App\Vectors\Vector;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use Tests\TestCase;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\PrintSheet;
use Illuminate\Support\Str;
use App\Models\PrintSheetItem;
use App\Services\PrintSheetService;

/**
 * Test service resources used for building print sheets
 *
 * @package Tests\Unit\Services
 *
 * @group Unit
 * @group Services
 * @group PrintSheetService
 *
 * @coversDefaultClass PrintSheetService
 */
class PrintSheetServiceTest extends TestCase
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
     * Given a print sheet request
     * When a valid Order is provided
     * Then a new print sheet can be created with associated sheet items
     *
     * @covers ::buildPrintSheetItems
     */
    final public function testCreateASinglePrintSheetItem(): void
    {
        $product = Product::factory()->unit()->make();
        $orderItem = OrderItem::factory([
            'product_id' => $product->getKey(),
            'quantity' => 1,
        ])
            ->unit()
            ->make()
            ->setRelation('product', $product);
        $printSheet = PrintSheet::factory()->unit()->make();
        $size = $product->getAttribute('size');
        $width = Str::before($size, 'x');
        $height = Str::after($size, 'x');
        $printSheetItem = $this->service->buildPrintSheetItems($printSheet, $orderItem)->first();

        self::assertEquals($printSheet->getKey(), $printSheetItem->print_sheet_id);
        self::assertEquals($orderItem->getKey(), $printSheetItem->order_item_id);
        self::assertEquals(PrintSheetItem::STATUS_PASS, $printSheetItem->status);
        self::assertEquals($width, $printSheetItem->width);
        self::assertEquals($height, $printSheetItem->height);
    }

    /**
     * Given a print sheet request
     * When a valid Order is provided with multiple order items
     * Then a new print sheet can be created with associated sheet items
     *
     * @covers ::buildPrintSheetItems
     */
    final public function testCreateMultiplePrintSheetItems(): void
    {
        $product = Product::factory()->unit()->make();
        $quantity = 5;
        $orderItem = OrderItem::factory([
            'product_id' => $product->getKey(),
            'quantity' => $quantity,
        ])
            ->unit()
            ->make()
            ->setRelation('product', $product);
        $printSheet = PrintSheet::factory()->unit()->make();
        $printSheetItems = $this->service->buildPrintSheetItems($printSheet, $orderItem);

        self::assertCount($quantity, $printSheetItems);

        $size = $product->getAttribute('size');
        $printSheetItems->each(function (PrintSheetItem $printSheetItem) use ($size) {
            $width = Str::before($size, 'x');
            $height = Str::after($size, 'x');
            self::assertEquals(PrintSheetItem::STATUS_PASS, $printSheetItem->status);
            self::assertEquals($width, $printSheetItem->width);
            self::assertEquals($height, $printSheetItem->height);
        });
    }

    /**
     * Given a collection of vectors
     * When these are passed to sortPrintSheetItems
     * Then items with largest side, prioritizing width, with come first.
     *
     * @dataProvider sheetItemProvider
     *
     * @param array $sheetItems
     * @param array $expected
     *
     * @covers ::sortPrintSheetItems
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
    #[ArrayShape(['single 5x2 should be top left' => "\int[][][]", 'variable amounts of each product' => "\int[][][]"])]
    final public function sheetItemProvider(): array
    {
        return [
            'single 5x2 should be top left' => [
                'sheetItems' => [
                    [1, 1],
                    [2, 2],
                    [2, 5],
                    [3, 3],
                    [5, 2],
                    [4, 4],
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
            'variable amounts of each product' => [
                'sheetItems' => [
                    [5, 2],
                    [2, 2],
                    [3, 3],
                    [2, 5],
                    [4, 4],
                    [5, 2],
                    [3, 3],
                    [2, 2],
                    [5, 2],
                    [1, 1],
                    [2, 5],
                    [1, 1],
                    [4, 4],
                ],
                'expected' => [
                    [5, 2, 1], [5, 2, 1], [5, 2, 1],
                    [2, 5, 1], [2, 5, 1],
                    [4, 4, 1], [4, 4, 1],
                    [3, 3, 1], [3, 3, 1],
                    [2, 2, 1], [2, 2, 1],
                    [1, 1, 1], [1, 1, 1],
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
