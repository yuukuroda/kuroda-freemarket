<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class T12_AddressTest extends TestCase
{
    use RefreshDatabase;

    private function prepareData()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'name' => 'テストユーザー名',
            'post_code' => '123-4567',
            'address' => '元の住所',
            'building' => '元のビル',
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
        ]);

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

    public function test_送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        [$user, $item] = $this->prepareData();

        $newAddressData = [
            'post_code' => '999-9999',
            'address'   => '変更後の住所',
            'building'  => '新しいビル名',
        ];

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->post(route('purchase.address.update', ['itemId' => $item->id]), $newAddressData);

        $response->assertStatus(302);
        $response->assertRedirect(route('purchase.create', ['itemId' => $item->id]));

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get(route('purchase.create', ['itemId' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee($newAddressData['post_code']);
        $response->assertSee($newAddressData['address']);
        $response->assertSee($newAddressData['building']);
    }

    public function test_購入した商品に送付先住所が紐づいて登録される()
    {
        [$user, $item] = $this->prepareData();

        $newAddressData = [
            'post_code' => '888-8888',
            'address'   => '購入用の住所',
            'building'  => '購入用のビル',
        ];

        $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->post(route('purchase.address.update', ['itemId' => $item->id]), $newAddressData);

        $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get(route('purchase.success', ['itemId' => $item->id]));

        $this->assertDatabaseHas('purchases', [
            'user_id'   => $user->id,
            'item_id'   => $item->id,
            'post_code' => $newAddressData['post_code'],
            'address'   => $newAddressData['address'],
            'building'  => $newAddressData['building'],
        ]);
    }
}
