<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Order
 *
 * @package App\Models
 * @property int $id
 * @property int $order_number
 * @property int|null $customer_id
 * @property float $total_price
 * @property string|null $fulfillment_status
 * @property string|null $fulfilled_date
 * @property string $order_status
 * @property int|null $customer_order_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|OrderItem[] $orderItems
 * @property-read int|null $order_items_count
 * @method static OrderFactory factory(...$parameters)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereCreatedAt(mixed $value)
 * @method static Builder|Order whereCustomerId(mixed $value)
 * @method static Builder|Order whereCustomerOrderCount(mixed $value)
 * @method static Builder|Order whereFulfilledDate(mixed $value)
 * @method static Builder|Order whereFulfillmentStatus(mixed $value)
 * @method static Builder|Order whereId(mixed $value)
 * @method static Builder|Order whereOrderNumber(mixed $value)
 * @method static Builder|Order whereOrderStatus(mixed $value)
 * @method static Builder|Order whereTotalPrice(mixed $value)
 * @method static Builder|Order whereUpdatedAt(mixed $value)
 * @mixin Eloquent
 */
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_RESEND = 'resend';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
        self::STATUS_DONE,
        self::STATUS_CANCELLED,
        self::STATUS_RESEND,
    ];

    final public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
