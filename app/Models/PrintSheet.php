<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrintSheetItem;

class PrintSheet extends Model
{
    use HasFactory;

    public const TYPE_ECOM = 'ecom';
    public const TYPE_TEST = 'test';

    public const TYPES = [
        self::TYPE_ECOM,
        self::TYPE_TEST,
    ];

    public function printSheetItems()
    {
        return $this->hasMany(PrintSheetItem::class);
    }
}
