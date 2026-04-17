<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class T13_ProfileGetTest extends TestCase
{
    use RefreshDatabase;

    private function prepareData()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test4@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id'   => $user->id,
            'name'      => '表示用ユーザー名',
            'image'     => 'profile_test.jpg',
            'post_code' => '123-4567',
            'address'   => '東京都',
        ]);

        $otherUser = User::create([
            'name' => '他のユーザー',
            'email' => 'other@example.com',
            'password' => Hash::make('password'),
        ]);

        $soldItem = Item::create([
            'user_id'     => $user->id,
            'name'        => '私が出品した商品',
            'description' => '説明',
            'price'       => 1000,
            'image'       => 'sold_item.jpg',
            'condition'   => '良好',
        ]);

        $purchasedItem = Item::create([
            'user_id'     => $otherUser->id,
            'name'        => '私が購入した商品',
            'description' => '説明',
            'price'       => 2000,
            'image'       => 'purchased_item.jpg',
            'condition'   => '良好',
        ]);

        Purchase::create([
            'user_id'   => $user->id,
            'item_id'   => $purchasedItem->id,
            'post_code' => '123-4567',
            'address'   => '配送先住所',
            'payment'   => 'カード支払い',
        ]);

        return [$user, $soldItem, $purchasedItem];
    }

    public function test_必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）()
    {
        [$user, $soldItem, $purchasedItem] = $this->prepareData();

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get('/mypage?page=sell');

        $response->assertStatus(200);

        $response->assertSee('表示用ユーザー名');
        $response->assertSee('profile_test.jpg');

        $response->assertSee('私が出品した商品');
        $response->assertDontSee('私が購入した商品');

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get('/mypage?page=buy');

        $response->assertStatus(200);

        $response->assertSee('私が購入した商品');
        $response->assertDontSee('私が出品した商品');
    }
}
