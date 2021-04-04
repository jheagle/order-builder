<?php

namespace App\Services;

use Exception;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintSheet;
use Illuminate\Support\Str;
use App\Vectors\VectorMatrix;
use App\Models\PrintSheetItem;
use Illuminate\Support\Collection;

/**
 * Manage Orders
 *
 * @package App\Services
 */
class PrintSheetService
{
    public const SHEET_WIDTH = 10;
    public const SHEET_HEIGHT = 15;

    /**
     * Take an Order an convert it to a Print Sheet
     *
     * @param Order $order
     *
     * @return PrintSheet
     *
     * @throws Exception
     */
    final public function buildPrintSheet(Order $order): PrintSheet
    {
        $printSheet = new PrintSheet();
        $printSheet->type = PrintSheet::TYPE_ECOM;
        $printSheet->sheet_url = '';
        $printSheet->save();

        $printSheetItems = $order->orderItems->reduce(
            fn(Collection $sheetItems, OrderItem $item) => $sheetItems->concat($this->buildPrintSheetItems($item)),
            new Collection()
        );

        (new VectorMatrix(self::SHEET_WIDTH, self::SHEET_HEIGHT))
            ->create()
            ->assignAvailablePositions($printSheetItems);

        $printSheet->printSheetItems()->saveMany($printSheetItems);

        return $printSheet;
    }

    /**
     * Build Sheet Items for a given Order Item
     *
     * @param OrderItem $item
     *
     * @return Collection
     */
    final public function buildPrintSheetItems(OrderItem $item): Collection
    {
        $sheetItems = new Collection();
        for ($i = 0; $i < $item->quantity; ++$i) {
            $printSheetItem = new PrintSheetItem();
            $printSheetItem->product_id = $item->product_id;
            $printSheetItem->order_item_id = $item->id;
            $printSheetItem->status = PrintSheetItem::STATUS_PASS;
            $printSheetItem->image_url = '';
            $product = $item->product;
            $printSheetItem->size = $product->size;
            $printSheetItem->x_pos = 0;
            $printSheetItem->y_pos = 0;
            $printSheetItem->width = preg_replace('/\s*(\d+)\s*x\s*\d+\s*/', '$1', $product->size);
            $printSheetItem->height = preg_replace('/\s*\d+\s*x\s*(\d+)\s*/', '$1', $product->size);
            $printSheetItem->identifier = Str::uuid();
            $sheetItems->push($printSheetItem);
        }
        return $sheetItems;
    }
}
