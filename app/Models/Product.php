<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Product
 *
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string|null $vendor
 * @property string|null $type
 * @property string|null $size
 * @property float $price
 * @property string|null $handle
 * @property int $inventory_quantity
 * @property string|null $sku
 * @property string|null $design_url
 * @property string $published_state
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|OrderItem[] $orderItems
 * @property-read int|null $order_items_count
 * @property-read Collection|PrintSheetItem[] $printSheetItems
 * @property-read int|null $print_sheet_items_count
 * @method static ProductFactory factory(...$parameters)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereCreatedAt(mixed $value)
 * @method static Builder|Product whereDesignUrl(mixed $value)
 * @method static Builder|Product whereHandle(mixed $value)
 * @method static Builder|Product whereId(mixed $value)
 * @method static Builder|Product whereInventoryQuantity(mixed $value)
 * @method static Builder|Product wherePrice(mixed $value)
 * @method static Builder|Product wherePublishedState(mixed $value)
 * @method static Builder|Product whereSize(mixed $value)
 * @method static Builder|Product whereSku(mixed $value)
 * @method static Builder|Product whereTitle(mixed $value)
 * @method static Builder|Product whereType(mixed $value)
 * @method static Builder|Product whereUpdatedAt(mixed $value)
 * @method static Builder|Product whereVendor(mixed $value)
 * @mixin Eloquent
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
