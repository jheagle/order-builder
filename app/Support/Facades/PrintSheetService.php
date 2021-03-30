<?php

namespace App\Support\Facades;

use App\Models\Order;
use App\Models\PrintSheet;
use Illuminate\Support\Facades\Facade;
use App\Services\PrintSheetService as Service;

/**
 * Make this service accessible and mockable.
 *
 * @package App\Support\Facades
 *
 * @const SHEET_WIDTH = 10
 * @const SHEET_HEIGHT = 15
 *
 * @method static Model assignAvailablePosition(Model $sheetItem, VectorMatrix $matrix)
 * @method static PrintSheet buildPrintSheet(Order $order)
 * @method static Collection buildPrintSheetItems(PrintSheet $printSheet, OrderItem $item)
 * @method static Collection sortPrintSheetItems(Collection $sheetItems)
 */
class PrintSheetService extends Facade
{
    /**
     * Return this Service class
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Service::class;
    }
}
