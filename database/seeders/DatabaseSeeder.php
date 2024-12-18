<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'last_name' => 'Mesca',
            'first_name' => 'Florin',
            'email' => 'florin@mesca.dev',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
    }
}
