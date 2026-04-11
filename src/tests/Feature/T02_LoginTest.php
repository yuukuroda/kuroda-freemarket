<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class T02_LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302);
        $this->get('/login')->assertSee('メールアドレスを入力してください');
    }

    public function test_パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
        $this->get('/login')->assertSee('パスワードを入力してください');
    }

    public function test_入力情報が間違っている場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $this->get('/login')->assertSee('ログイン情報が登録されていません');
    }

    public function test_正しい情報が入力された場合、ログイン処理が実行される()
    {
        $user = User::factory()->create([
            'email' => 'valid@example.com',
            'password' => Hash::make('correct_password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'valid@example.com',
            'password' => 'correct_password',
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }
}
