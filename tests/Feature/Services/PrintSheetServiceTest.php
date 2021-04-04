<?php

namespace Tests\Feature\Services;

use Exception;
use Tests\TestCase;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\PrintSheet;
use App\Services\PrintSheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test service resources used for building print sheets
 *
 * @package Tests\Feature\Services
 *
 * @group Feature
 * @group Services
 * @group PrintSheetService
 *
 * @coversDefaultClass PrintSheetService
 */
class PrintSheetServiceTest extends TestCase
{
    use RefreshDatabase;

    public PrintSheetService $service;

    /**
     * Set up these tests.
     *
     * @return void
     */
    final public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->service = new PrintSheetService;
    }

    /**
     * Given a print sheet request
     * When a valid Order is provided
     * Then a new print sheet can be created with associated sheet items
     *
     * @covers ::buildPrintSheet
     *
     * @throws Exception
     */
    final public function testCreateASingleItemPrintSheet(): void
    {
        $product = Product::find(3);
        $quantity = 1;
        $orderItem = OrderItem::factory([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ])->create();
        $order = $orderItem->order;

        $printSheet = $this->service->buildPrintSheet($order);

        $this->assertDatabaseHas('print_sheets', [
            'id' => 1,
            'type' => PrintSheet::TYPE_ECOM,
        ]);

        self::assertCount($quantity, $printSheet->printSheetItems);
    }

    /**
     * Given a print sheet request
     * When a valid Order is provided
     * Then a new print sheet can be created with associated sheet items
     *
     * @covers ::buildPrintSheet
     *
     * @throws Exception
     */
    final public function testCreateMultipleItemPrintSheet(): void
    {
        $product = Product::find(5);
        $quantity = 4;
        $orderItem = OrderItem::factory([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ])->create();
        $order = $orderItem->order;

        $printSheet = $this->service->buildPrintSheet($order);

        $this->assertDatabaseHas('print_sheets', [
            'id' => 1,
            'type' => PrintSheet::TYPE_ECOM,
        ]);

        self::assertCount($quantity, $printSheet->printSheetItems);
        $this->assertDatabaseHas('print_sheet_items', [
            'id' => 1,
            'print_sheet_id' => 1,
            'x_pos' => 0,
            'y_pos' => 0,
        ]);
        $this->assertDatabaseHas('print_sheet_items', [
            'id' => 2,
            'print_sheet_id' => 1,
            'x_pos' => 5,
            'y_pos' => 0,
        ]);
        $this->assertDatabaseHas('print_sheet_items', [
            'id' => 3,
            'print_sheet_id' => 1,
            'x_pos' => 0,
            'y_pos' => 2,
        ]);
        $this->assertDatabaseHas('print_sheet_items', [
            'id' => 4,
            'print_sheet_id' => 1,
            'x_pos' => 5,
            'y_pos' => 2,
        ]);
    }
}
