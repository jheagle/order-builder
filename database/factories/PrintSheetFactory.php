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
    public function definition()
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
    public function unit()
    {
        return $this->state(
            fn () => [
                'id' => mt_rand(1, 999),
            ]
        );
    }
}
