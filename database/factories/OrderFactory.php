<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    final public function definition(): array
    {
        return [
            'order_number' => $this->faker->randomNumber(),
            'customer_id' => $this->faker->randomNumber(),
            'total_price' => $this->faker->randomFloat(2, 1, 1000),
            'fulfillment_status' => $this->faker->text(25),
            'order_status' => $this->faker->randomElement(Order::STATUSES),
            'fulfilled_date' => now(),
            'customer_order_count' => $this->faker->randomNumber(),
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
            ]
        );
    }
}
