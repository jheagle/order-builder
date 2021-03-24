<?php

namespace Tests\Unit\Services;

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
            'product_id' => $product->id,
            'quantity' => 1,
        ])->unit()->make();
        $orderItem->product = $product;
        $printSheet = PrintSheet::factory()->unit()->make();
        $width = Str::before($product->size, 'x');
        $height = Str::after($product->size, 'x');
        $printSheetItem = $this->service->buildPrintSheetItems($printSheet, $orderItem)->first();

        self::assertEquals($printSheet->id, $printSheetItem->print_sheet_id);
        self::assertEquals($orderItem->id, $printSheetItem->order_item_id);
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
            'product_id' => $product->id,
            'quantity' => $quantity,
        ])->unit()->make();
        $orderItem->product = $product;
        $printSheet = PrintSheet::factory()->unit()->make();
        $printSheetItems = $this->service->buildPrintSheetItems($printSheet, $orderItem);

        self::assertCount($quantity, $printSheetItems);

        $printSheetItems->each(function (PrintSheetItem $printSheetItem) use ($product) {
            $width = Str::before($product->size, 'x');
            $height = Str::after($product->size, 'x');
            self::assertEquals(PrintSheetItem::STATUS_PASS, $printSheetItem->status);
            self::assertEquals($width, $printSheetItem->width);
            self::assertEquals($height, $printSheetItem->height);
        });
    }
}
