<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Product;
use App\Models\PrintSheetItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function printSheetItems()
    {
        return $this->hasMany(PrintSheetItem::class);
    }
}
