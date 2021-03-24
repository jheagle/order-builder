<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static find(int $productId)
 */
class Product extends Model
{
    use HasFactory;

    final public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    final public function printSheetItems(): HasMany
    {
        return $this->hasMany(PrintSheetItem::class);
    }
}
