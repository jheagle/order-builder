<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(100),
            'vendor' => $this->faker->text(50),
            'type' => $this->faker->text(25),
            'size' => $this->faker->text(20),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'handle' => $this->faker->text(75),
            'inventory_quantity' => mt_rand(1, 999),
            'sku' => $this->faker->text(30),
            'design_url' => $this->faker->domainName(),
            'published_state' => $this->faker->randomElement(['inactive', 'active']),
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
            ]
        );
    }
}
