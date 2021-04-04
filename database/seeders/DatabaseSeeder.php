<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    final public function run(): void
    {
        $sizes = ['1x1', '2x2', '3x3', '4x4', '5x2', '2x5'];
        for ($i = 0; $i < 6; ++$i) {
            Product::factory()->create([
                'title' => 'Product ' . $i + 1,
                'size' => $sizes[$i],
                'published_state' => 'active',
            ]);
        }
    }
}
