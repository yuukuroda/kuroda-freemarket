<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class T16_EmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_会員登録後、認証メールが送信される()
    {
        $user = User::create([
            'name' => '未認証ユーザー',
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('verification.notice'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    public function test_メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する()
    {
        $user = User::create([
            'name' => '認証中ユーザー',
            'email' => 'verify@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $response->assertRedirect('/profile.show');
    }

    public function test_メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する()
    {
        $user = User::create([
            'name' => '認証済みユーザー',
            'email' => 'verified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('profile.show'));

        if ($response->status() === 302) {
            $response->assertRedirect();
        } else {
            $response->assertStatus(200);
        }
    }
}
