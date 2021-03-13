<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
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
     */
    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => mt_rand(1, 150),
            'refunded' => $this->faker->randomNumber(),
            'resend_amount' => $this->faker->randomNumber(),
        ];
    }

    /**
     * Mock out IDs.
     *
     * @return Factory
     */
    public function unit()
    {
        return $this->state(
            fn () => [
                'id' => mt_rand(1, 999),
                'order_id' => mt_rand(1, 999),
                'product_id' => mt_rand(1, 999),
            ]
        );
    }
}
