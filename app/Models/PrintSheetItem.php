<?php

namespace App\Models;

use App\Vectors\VectorModel;
use Database\Factories\PrintSheetItemFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class PrintSheetItem
 *
 * @package App\Models
 * @property int $id
 * @property int $print_sheet_id
 * @property int $product_id
 * @property int $order_item_id
 * @property string $status
 * @property string $image_url
 * @property string $size
 * @property int $x_pos
 * @property int $y_pos
 * @property int $width
 * @property int $height
 * @property string $identifier
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read OrderItem $orderItem
 * @property-read PrintSheet $printSheet
 * @property-read Product $product
 * @method static PrintSheetItemFactory factory(...$parameters)
 * @method static Builder|PrintSheetItem newModelQuery()
 * @method static Builder|PrintSheetItem newQuery()
 * @method static Builder|PrintSheetItem query()
 * @method static Builder|PrintSheetItem whereCreatedAt(mixed $value)
 * @method static Builder|PrintSheetItem whereHeight(mixed $value)
 * @method static Builder|PrintSheetItem whereId(mixed $value)
 * @method static Builder|PrintSheetItem whereIdentifier(mixed $value)
 * @method static Builder|PrintSheetItem whereImageUrl(mixed $value)
 * @method static Builder|PrintSheetItem whereOrderItemId(mixed $value)
 * @method static Builder|PrintSheetItem wherePrintSheetId(mixed $value)
 * @method static Builder|PrintSheetItem whereProductId(mixed $value)
 * @method static Builder|PrintSheetItem whereSize(mixed $value)
 * @method static Builder|PrintSheetItem whereStatus(mixed $value)
 * @method static Builder|PrintSheetItem whereUpdatedAt(mixed $value)
 * @method static Builder|PrintSheetItem whereWidth(mixed $value)
 * @method static Builder|PrintSheetItem whereXPos(mixed $value)
 * @method static Builder|PrintSheetItem whereYPos(mixed $value)
 * @mixin Eloquent
 */
class PrintSheetItem extends VectorModel
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

    protected array $coordinates = [
        'x' => 'x_pos',
        'y' => 'y_pos',
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
