<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class T11_MethodTest extends TestCase
{
    use RefreshDatabase;

    private function prepareData()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id'   => $user->id,
            'name'      => 'テストユーザー名',
            'post_code' => '123-4567',
            'address'   => '東京都新宿区',
            'building'  => 'テストビル',
            'image'     => 'profile.jpg',
        ]);

        $item = Item::create([
            'user_id'     => $user->id,
            'name'        => 'テスト商品',
            'description' => 'テスト説明',
            'price'       => 1000,
            'image'       => 'test.jpg',
            'condition'   => '良好',
        ]);

        return [$user, $item];
    }

    public function test_小計画面で変更が反映される()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get(route('purchase.create', ['itemId' => $item->id]));

        if ($response->status() !== 200) {
            $response->dump();
        }

        $response->assertStatus(200);

        $response->assertSee('id="payment_method_select"', false);

        $response->assertSee('コンビニ支払い');
        $response->assertSee('カード支払い');

        $response->assertSee('id="display_payment"', false);

        $response->assertSee('paymentSelect.addEventListener(\'change\'', false);
        $response->assertSee('displayPayment.textContent = selectedText', false);
    }
}
