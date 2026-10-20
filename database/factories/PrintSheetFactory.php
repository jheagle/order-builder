<?php

namespace Database\Factories;

use App\Models\PrintSheet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrintSheetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrintSheet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    final public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(PrintSheet::TYPES),
            'sheet_url' => $this->faker->domainName(),
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
