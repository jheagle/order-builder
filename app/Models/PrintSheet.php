<?php

namespace App\Models;

use Database\Factories\PrintSheetFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class PrintSheet
 *
 * @package App\Models
 * @property int $id
 * @property string $type
 * @property string $sheet_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|PrintSheetItem[] $printSheetItems
 * @property-read int|null $print_sheet_items_count
 * @method static PrintSheetFactory factory(...$parameters)
 * @method static Builder|PrintSheet newModelQuery()
 * @method static Builder|PrintSheet newQuery()
 * @method static Builder|PrintSheet query()
 * @method static Builder|PrintSheet whereCreatedAt(mixed $value)
 * @method static Builder|PrintSheet whereId(mixed $value)
 * @method static Builder|PrintSheet whereSheetUrl(mixed $value)
 * @method static Builder|PrintSheet whereType(mixed $value)
 * @method static Builder|PrintSheet whereUpdatedAt(mixed $value)
 * @mixin Eloquent
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
