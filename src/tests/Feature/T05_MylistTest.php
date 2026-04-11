<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Good;

class T05_MylistTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_いいねした商品だけが表示される()
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $other = User::create(['name' => 'Other', 'email' => 'other_' . uniqid() . '@example.com', 'password' => 'pass']);

        $itemA = Item::create([
            'user_id' => $other->id,
            'image' => 'item_a.jpg',
            'condition' => '良好',
            'name' => 'いいねした商品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $itemB = Item::create([
            'user_id' => $other->id,
            'image' => 'item_b.jpg',
            'condition' => '良好',
            'name' => 'いいねしていない商品',
            'description' => '説明',
            'price' => 2000,
        ]);

        Good::create([
            'user_id' => $user->id,
            'item_id' => $itemA->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }

    public function test_購入済み商品は「Sold」と表示される()
    {
        $user = User::create(['name' => 'User', 'email' => 'u_' . uniqid() . '@example.com', 'password' => 'pass']);
        $other = User::create(['name' => 'Other', 'email' => 'o_' . uniqid() . '@example.com', 'password' => 'pass']);

        $item = Item::create([
            'user_id' => $other->id,
            'image' => 'sold_item.jpg',
            'condition' => '良好',
            'name' => '完売したいいね商品',
            'description' => '説明',
            'price' => 1500,
        ]);

        Good::create(['user_id' => $user->id, 'item_id' => $item->id]);

        Purchase::create([
            'user_id' => $other->id,
            'item_id' => $item->id,
            'post_code' => '123-4567',
            'address' => '住所',
            'building' => 'ビル',
            'payment' => 'カード',
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('完売したいいね商品');
        $response->assertSee('Sold');
    }

    public function test_未認証の場合は何も表示されない()
    {
        $seller = User::create(['name' => 'Seller', 'email' => 's_' . uniqid() . '@example.com', 'password' => 'pass']);
        Item::create([
            'user_id' => $seller->id,
            'image' => 'item.jpg',
            'condition' => '良好',
            'name' => 'テスト商品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        $response->assertDontSee('テスト商品');
    }
}
