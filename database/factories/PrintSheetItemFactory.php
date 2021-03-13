<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\PrintSheet;
use App\Models\PrintSheetItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrintSheetItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrintSheetItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::factory()->create();
        $width = mt_rand(1, 5);
        $height = $width === 5 ? 2 : mt_rand(1, 5);
        $width = $height === 5 ? 2 : $width;
        return [
            'print_sheet_id' => PrintSheet::factory()->create(),
            'product_id' => $product,
            'order_item_id' => OrderItem::factory()->create([
                'product_id' => $product
            ]),
            'status' => $this->faker->randomElement(['pass', 'reject', 'complete']),
            'image_url' => $this->faker->domainName(),
            'size' => "{$width}x{$height}",
            'x_pos' => mt_rand(0, 10 - $width),
            'y_pos' => mt_rand(0, 15 - $height),
            'width' => $width,
            'height' => $height,
            'identifier' => $this->faker->text(255),
        ];
    }

    /**
     * Mock out IDs
     *
     * @return Factory
     */
    public function unit()
    {
        return $this->state(
            fn () => [
                'id' => mt_rand(1, 999),
                'print_sheet_id' => mt_rand(1, 999),
                'product_id' => mt_rand(1, 999),
                'order_item_id' => mt_rand(1, 999),
            ]
        );
    }
}
