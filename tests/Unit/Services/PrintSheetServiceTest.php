<?php

namespace Tests\Unit\Services;

use JetBrains\PhpStorm\ArrayShape;
use Tests\TestCase;
use App\Models\Product;
use App\Models\OrderItem;
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
        $size = $product->getAttribute('size');
        $width = Str::before($size, 'x');
        $height = Str::after($size, 'x');
        $printSheetItem = $this->service->buildPrintSheetItems($orderItem)->first();

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
        $printSheetItems = $this->service->buildPrintSheetItems($orderItem);

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
}
