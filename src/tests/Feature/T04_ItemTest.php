<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class T04_ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_全商品を取得できる()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
        ]);

        Item::create([
            'user_id' => $seller->id,
            'image' => 'item_a.jpg',
            'condition' => '良好',
            'name' => 'テスト商品A',
            'description' => '説明',
            'price' => 1000,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('テスト商品A');
    }

    public function test_購入済み商品は「Sold」と表示される()
    {
        $seller = User::create([
            'name' => 'Seller',
            'email' => 's_' . uniqid() . '@example.com',
            'password' => 'pass'
        ]);
        $buyer = User::create([
            'name' => 'Buyer',
            'email' => 'b_' . uniqid() . '@example.com',
            'password' => 'pass'
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'image' => 'sold_item.jpg',
            'condition' => '良好',
            'name' => '完売した商品',
            'description' => '説明',
            'price' => 1500,
        ]);

        Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'post_code' => '123-4567',
            'address' => '東京都渋谷区...',
            'building' => 'テストビル',
            'payment' => 'コンビニ払い'
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('完売した商品');
        $response->assertSee('Sold');
    }


    public function test_自分が出品した商品は表示されない()
    {
        $me = User::create(['name' => 'Me', 'email' => 'me@example.com', 'password' => bcrypt('password')]);
        $other = User::create(['name' => 'Other', 'email' => 'other@example.com', 'password' => 'pass']);

        Item::create([
            'user_id' => $me->id,
            'image' => 'my_item.jpg',
            'condition' => '良好',
            'name' => '私の出品した商品',
            'description' => '非表示',
            'price' => 5000,
        ]);

        Item::create([
            'user_id' => $other->id,
            'image' => 'other_item.jpg',
            'condition' => '良好',
            'name' => '他人の出品した商品',
            'description' => '表示',
            'price' => 3000,
        ]);

        $response = $this->actingAs($me)->get('/');

        $response->assertStatus(200);
        $response->assertSee('他人の出品した商品');
        $response->assertDontSee('私の出品した商品');
    }
}
