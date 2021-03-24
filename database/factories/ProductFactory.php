<?php

namespace Database\Factories;

use App\Models\Product;
use Exception;
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
     *
     * @throws Exception
     */
    final public function definition(): array
    {
        $skuLetters = explode(',', 'B,C,D,F,G,H,J,K,L,M,N,P,Q,R,S,T,V,W,X,Y,Z');
        $skuLength = random_int(3, 6);
        $sku = '';
        for ($i = 0; $i < $skuLength; ++$i) {
            $sku .= $skuLetters[random_int(0, count($skuLetters) - 1)];
        }
        $size = random_int(1, 5) . 'x' . random_int(1, 5);
        return [
            'title' => $this->faker->text(100),
            'vendor' => $this->faker->text(50),
            'type' => $this->faker->text(25),
            'size' => $size,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'handle' => $this->faker->text(75),
            'inventory_quantity' => random_int(1, 999),
            'sku' => $sku,
            'design_url' => $this->faker->domainName(),
            'published_state' => $this->faker->randomElement(['inactive', 'active']),
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
