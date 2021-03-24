<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|mixed order_number
 * @property int|mixed customer_id
 * @property mixed|string order_status
 * @property mixed total_price
 * @property mixed id
 * @property mixed orderItems
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
