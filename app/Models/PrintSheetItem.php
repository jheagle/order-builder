<?php

namespace App\Models;

use App\Models\Product;
use App\Models\OrderItem;
use App\Models\PrintSheet;
use App\Vectors\Traits\HasVectors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrintSheetItem extends Model
{
    use HasFactory;
    use HasVectors;

    public const STATUS_PASS = 'pass';
    public const STATUS_REJECT = 'reject';
    public const STATUS_COMPLETE = 'complete';

    public const STATUSES = [
        self::STATUS_PASS,
        self::STATUS_REJECT,
        self::STATUS_COMPLETE,
    ];

    protected array $coordinates = [
        'x' => 'x_pos',
        'y' => 'y_pos',
    ];

    protected array $dimensions = [
        'width' => 'width',
        'height' => 'height',
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
