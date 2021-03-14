<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\PrintSheet;
use App\Models\Product;

class PrintSheetItem extends Model
{
    use HasFactory;

    public const STATUS_PASS = 'pass';
    public const STATUS_REJECT = 'reject';
    public const STATUS_COMPLETE = 'complete';

    public const STATUSES = [
        self::STATUS_PASS,
        self::STATUS_REJECT,
        self::STATUS_COMPLETE,
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function printSheet()
    {
        return $this->belongsTo(PrintSheet::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
