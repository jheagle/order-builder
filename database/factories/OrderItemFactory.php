<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     *
     * @throws Exception
     */
    final public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => random_int(1, 150),
            'refunded' => $this->faker->randomNumber(),
            'resend_amount' => $this->faker->randomNumber(),
        ];
    }

    /**
     * Mock out IDs.
     *
     * @return Factory
     */
    final public function unit(): Factory
    {
        return $this->state(
            fn() => [
                'id' => random_int(1, 999),
                'order_id' => random_int(1, 999),
                'product_id' => random_int(1, 999),
            ]
        );
    }
}
