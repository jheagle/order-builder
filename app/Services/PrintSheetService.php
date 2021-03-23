<?php

namespace App\Services;

use Exception;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintSheet;
use Illuminate\Support\Str;
use App\Vectors\VectorMatrix;
use App\Models\PrintSheetItem;
use App\Vectors\Traits\HasVectors;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

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
    public function buildPrintSheet(Order $order): PrintSheet
    {
        $printSheet = new PrintSheet();
        $printSheet->type = PrintSheet::TYPE_ECOM;
        $printSheet->sheet_url = '';
        $printSheet->save();

        $printSheetItems = $order->orderItems->reduce(
            fn(Collection $sheetItems, OrderItem $item) => $sheetItems->concat($this->buildPrintSheetItems($printSheet, $item)),
            new Collection()
        );

        $matrix = (new VectorMatrix(self::SHEET_WIDTH, self::SHEET_HEIGHT))->create();
        $this->sortPrintSheetItems($printSheetItems)
            ->each(function (PrintSheetItem $sheetItem) use ($matrix) {
                $this->assignAvailablePosition($sheetItem, $matrix);
                $sheetItem->save();
            });

        return $printSheet;
    }

    /**
     * Build Sheet Items for a given Order Item
     *
     * @param PrintSheet $printSheet
     * @param OrderItem $item
     *
     * @return Collection
     */
    public function buildPrintSheetItems(PrintSheet $printSheet, OrderItem $item): Collection
    {
        $sheetItems = new Collection();
        for ($i = 0; $i < $item->quantity; ++$i) {
            $printSheetItem = new PrintSheetItem();
            $printSheetItem->print_sheet_id = $printSheet->id;
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

    /**
     * Take all of the Print Sheet Items and sort them from largest perimeter to smallest.
     * Equal perimeter place largest width first.
     *
     * @param Collection $sheetItems
     *
     * @return Collection
     */
    public function sortPrintSheetItems(Collection $sheetItems): Collection
    {
        return $sheetItems->sort(function (PrintSheetItem $a, PrintSheetItem $b) {
            $highestWidth = $a->width > $b->width ? $a : $b;
            $highestHeight = $a->height > $b->height ? $a : $b;
            $largestSide = $highestHeight->height > $highestWidth->width ? $highestHeight : $highestWidth;
            return $largestSide === $a ? -1 : 1;
        })->values();
    }

    /**
     * Assign next available space to the Print Sheet Item
     *
     * @param Model $sheetItem
     * @param VectorMatrix $matrix
     *
     * @return Model
     *
     * @throws Exception
     */
    public function assignAvailablePosition(Model $sheetItem, VectorMatrix $matrix): Model
    {
        if (!in_array(HasVectors::class, class_uses($sheetItem), true)) {
            throw new Exception('There are no vectors on ' . get_class($sheetItem));
        }
        $foundSpace = false;
        $sheetVectors = $sheetItem->getVectors();
        if ($sheetVectors->count() > $matrix->getAvailableVectors()->count()) {
            throw new Exception('There is no available space for ' . get_class($sheetItem));
        }
        foreach ($matrix->getAvailableVectors() as $availVector) {
            $sheetItem->setAnchorPoint($availVector);
            if ($matrix->canUseVectors($sheetItem->getVectors())) {
                $matrix->assignVectors($sheetVectors);
                $foundSpace = true;
                break;
            }
            $sheetVectors = $sheetItem->getVectors();
        }
        if (!$foundSpace) {
            throw new Exception('There is no available space for ' . get_class($sheetItem));
        }
        return $sheetItem;
    }
}
