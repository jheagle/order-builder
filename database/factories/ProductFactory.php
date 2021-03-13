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
        $skuLetters = explode(',', 'B,C,D,F,G,H,J,K,L,M,N,P,Q,R,S,T,V,W,X,Y,Z');
        $skuLength = mt_rand(3, 6);
        $sku = '';
        for ($i = 0; $i < $skuLength; ++$i) {
            $sku .= $skuLetters[mt_rand(0, count($skuLetters) - 1)];
        }
        return [
            'title' => $this->faker->text(100),
            'vendor' => $this->faker->text(50),
            'type' => $this->faker->text(25),
            'size' => $this->faker->text(20),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'handle' => $this->faker->text(75),
            'inventory_quantity' => mt_rand(1, 999),
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
    public function unit()
    {
        return $this->state(
            fn () => [
                'id' => mt_rand(1, 999),
            ]
        );
    }
}
