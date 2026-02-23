<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
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
        $this->call(CategoriesTableSeeder::class);
        // \App\Models\User::factory(10)->create();
        User::create([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'id' => 2,
            'name' => '山田',
            'email' => 'ymd@ymd',
            'password' => Hash::make('yyyyuuuu'),
        ]);
        Profile::create([
            'user_id' => 1,
            'name' => 'Test User',
            'image' => 'profiles/dog1.jfif',
            'post_code' => '1234567',
            'address' => 'testcity',
            'building' => 'testtower',
        ]);
        Profile::create([
            'user_id' => 2,
            'name' => '山田',
            'image' => 'profiles/dog2.jfif',
            'post_code' => '1234567',
            'address' => 'yamadacity',
            'building' => 'yamadatower',
        ]);

        $this->call(ItemsTableSeeder::class);
        $this->call(PurchasesTableSeeder::class);
    }
}
