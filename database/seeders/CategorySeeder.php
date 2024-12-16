<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->create([
            'name' => 'No category',
            'description' => 'Default category',
        ]);

        Category::factory()->create([
            'name' => 'Laptops',
            'description' => 'Devices for personal computing',
        ]);

        Category::factory()->create([
            'name' => 'PCs',
            'description' => 'Desktop computers for work and gaming',
        ]);

        Category::factory()->create([
            'name' => 'Phones',
            'description' => 'Mobile devices for communication',
        ]);
    }
}
