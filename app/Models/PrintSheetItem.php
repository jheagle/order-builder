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
