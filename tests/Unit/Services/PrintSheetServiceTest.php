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
    protected function setUp(): void
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
    public function testCreateASinglePrintSheetItem()
    {
        $product = Product::factory()->unit()->make();
        $orderItem = OrderItem::factory()->unit()
            ->make([
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        $orderItem->product = $product;
        $printSheet = PrintSheet::factory()->unit()->make();
        $width = Str::before($product->size, 'x');
        $height = Str::after($product->size, 'x');
        $printSheetItem = $this->service->buildPrintSheetItems($printSheet, $orderItem)->first();

        $this->assertEquals($printSheet->id, $printSheetItem->print_sheet_id);
        $this->assertEquals($orderItem->id, $printSheetItem->order_item_id);
        $this->assertEquals(PrintSheetItem::STATUS_PASS, $printSheetItem->status);
        $this->assertEquals($width, $printSheetItem->width);
        $this->assertEquals($height, $printSheetItem->height);
    }

    /**
     * Given a print sheet request
     * When a valid Order is provided with multiple order items
     * Then a new print sheet can be created with associated sheet items
     *
     * @covers ::buildPrintSheetItems
     */
    public function testCreateMultiplePrintSheetItems()
    {
        $product = Product::factory()->unit()->make();
        $quantity = 5;
        $orderItem = OrderItem::factory()->unit()
            ->make([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        $orderItem->product = $product;
        $printSheet = PrintSheet::factory()->unit()->make();
        $printSheetItems = $this->service->buildPrintSheetItems($printSheet, $orderItem);

        $this->assertCount($quantity, $printSheetItems);

        $printSheetItems->each(function (PrintSheetItem $printSheetItem) use($product){
            $width = Str::before($product->size, 'x');
            $height = Str::after($product->size, 'x');
            $this->assertEquals(PrintSheetItem::STATUS_PASS, $printSheetItem->status);
            $this->assertEquals($width, $printSheetItem->width);
            $this->assertEquals($height, $printSheetItem->height);
        });
    }
}
