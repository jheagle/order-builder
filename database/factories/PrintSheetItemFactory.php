<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\PrintSheet;
use App\Models\PrintSheetItem;
use App\Models\Product;
use Exception;
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
     *
     * @throws Exception
     */
    final public function definition():array
    {
        $product = Product::factory();
        $width = random_int(1, 5);
        $height = $width === 5 ? 2 : random_int(1, 5);
        $width = $height === 5 ? 2 : $width;
        return [
            'print_sheet_id' => PrintSheet::factory(),
            'product_id' => $product,
            'order_item_id' => OrderItem::factory([
                'product_id' => $product
            ]),
            'status' => $this->faker->randomElement(PrintSheetItem::STATUSES),
            'image_url' => $this->faker->domainName(),
            'size' => "{$width}x{$height}",
            'x_pos' => random_int(0, 10 - $width),
            'y_pos' => random_int(0, 15 - $height),
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
    final public function unit():Factory
    {
        return $this->state(
            fn () => [
                'id' => random_int(1, 999),
                'print_sheet_id' => random_int(1, 999),
                'product_id' => random_int(1, 999),
                'order_item_id' => random_int(1, 999),
            ]
        );
    }
}
