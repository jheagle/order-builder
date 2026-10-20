<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class OrderItem
 *
 * @package App\Models
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property int $refunded
 * @property int $resend_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order $order
 * @property-read Collection|PrintSheetItem[] $printSheetItems
 * @property-read int|null $print_sheet_items_count
 * @property-read Product $product
 * @method static OrderItemFactory factory(...$parameters)
 * @method static Builder|OrderItem newModelQuery()
 * @method static Builder|OrderItem newQuery()
 * @method static Builder|OrderItem query()
 * @method static Builder|OrderItem whereCreatedAt(mixed $value)
 * @method static Builder|OrderItem whereId(mixed $value)
 * @method static Builder|OrderItem whereOrderId(mixed $value)
 * @method static Builder|OrderItem whereProductId(mixed $value)
 * @method static Builder|OrderItem whereQuantity(mixed $value)
 * @method static Builder|OrderItem whereRefunded(mixed $value)
 * @method static Builder|OrderItem whereResendAmount(mixed $value)
 * @method static Builder|OrderItem whereUpdatedAt(mixed $value)
 * @mixin \Eloquent
 */
class OrderItem extends Model
{
    use HasFactory;

    final public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    final public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    final public function printSheetItems(): HasMany
    {
        return $this->hasMany(PrintSheetItem::class);
    }
}
