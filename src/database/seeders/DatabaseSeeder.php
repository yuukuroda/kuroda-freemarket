<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::create([
            'id' => 1, // 明示的に1を指定
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // Fortifyのログインでも使えるパスワード
        ]);

        $this->call(ItemsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
    }
}
