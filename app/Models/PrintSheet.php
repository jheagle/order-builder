<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed|string type
 * @property mixed|string sheet_url
 * @property mixed id
 * @property mixed printSheetItems
 */
class PrintSheet extends Model
{
    use HasFactory;

    public const TYPE_ECOM = 'ecom';
    public const TYPE_TEST = 'test';

    public const TYPES = [
        self::TYPE_ECOM,
        self::TYPE_TEST,
    ];

    final public function printSheetItems(): HasMany
    {
        return $this->hasMany(PrintSheetItem::class);
    }
}
