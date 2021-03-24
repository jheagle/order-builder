<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed order_id
 * @property int|mixed product_id
 * @property int|mixed quantity
 * @property mixed product
 * @property mixed id
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
