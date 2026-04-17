<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class T14_ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    private function prepareData()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test5@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $profile = Profile::create([
            'user_id'   => $user->id,
            'image'     => 'existing_profile_image.png',
            'name'      => '設定済みユーザー名',
            'post_code' => '987-6543',
            'address'   => '東京都渋谷区道玄坂',
            'building'  => 'テックビル101',
        ]);

        return [$user, $profile];
    }

    public function test_変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）()
    {
        [$user, $profile] = $this->prepareData();

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get(route('profile.show'));

        $response->assertStatus(200);

        $response->assertSee('value="' . $profile->name . '"', false);

        $response->assertSee('value="' . $profile->post_code . '"', false);

        $response->assertSee('value="' . $profile->address . '"', false);

        $response->assertSee('value="' . $profile->building . '"', false);

        $response->assertSee($profile->image);
    }
}
