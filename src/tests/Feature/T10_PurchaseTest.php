<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class T10_PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テストデータの準備
     */
    private function prepareData()
    {
        // 購入者
        $user = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'name' => '購入者名',
            'post_code' => '123-4567',
            'address' => '東京都新宿区',
        ]);

        // 出品者
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // 商品
        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'description' => '説明',
            'price' => 1000,
            'image' => 'test.jpg',
            'condition' => '良好',
        ]);

        return [$user, $item];
    }

    /**
     * 1. 「購入する」ボタンを押下すると購入が完了する
     */
    public function test_「購入する」ボタンを押下すると購入が完了する()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get(route('purchase.success', ['itemId' => $item->id]));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response->assertRedirect('/');
    }

    public function test_購入した商品は商品一覧画面にて「sold」と表示される()
    {
        [$user, $item] = $this->prepareData();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'post_code' => '111-1111',
            'address' => '住所',
            'payment' => 'カード支払い',
        ]);

        $response = $this->get('/');

        $response->assertSee('Sold', false);
    }

    public function test_「プロフィール／購入した商品一覧」に追加されている()
    {
        [$user, $item] = $this->prepareData();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'post_code' => '111-1111',
            'address' => '住所',
            'payment' => 'カード支払い',
        ]);

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get('/mypage?page=buy');

        $response->assertStatus(200);

        $response->assertSee($item->name);
    }
}
