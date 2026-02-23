<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purchases = [
            [
                'user_id' => '1',
                'item_id' => '1',
                'post_code' => '1234567',
                'address' => 'house',
                'building' => '',
                'payment' => 'コンビニ支払い',
            ],

        ];
        DB::table('purchases')->insert($purchases);
    }
}
