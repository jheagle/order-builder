<?php

namespace App\Models;

use App\Vectors\Traits\HasVectors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed print_sheet_id
 * @property int|mixed product_id
 * @property mixed order_item_id
 * @property mixed|string status
 * @property mixed|string image_url
 * @property mixed size
 * @property int|mixed x_pos
 * @property int|mixed y_pos
 * @property mixed|string|string[]|null width
 * @property mixed|string|string[]|null height
 * @property mixed identifier
 */
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

    final public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    final public function printSheet(): BelongsTo
    {
        return $this->belongsTo(PrintSheet::class);
    }

    final public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
