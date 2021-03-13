<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrintSheetItem;

class PrintSheet extends Model
{
    use HasFactory;

    public function printSheetItems()
    {
        return $this->hasMany(PrintSheetItem::class);
    }
}
