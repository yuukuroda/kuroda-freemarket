<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class T01_RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    protected function validData($overrides = [])
    {
        return array_merge([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_名前が入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', $this->validData(['name' => '']));

        $response->assertSessionHasErrors(['name']);
        $response->assertStatus(302);
        $this->get('/register')->assertSee('ユーザー名を入力してください');
    }

    public function test_メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', $this->validData(['email' => '']));

        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302);
        $this->get('/register')->assertSee('メールアドレスを入力してください');
    }

    public function test_パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', $this->validData(['password' => '', 'password_confirmation' => '']));

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
        $this->get('/register')->assertSee('パスワードを入力してください');
    }

    public function test_パスワードが7文字以下の場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', $this->validData([
            'password' => 'pass123',
            'password_confirmation' => 'pass123'
        ]));

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
        $this->get('/register')->assertSee('パスワードは8文字以上で入力してください');
    }

    public function test_パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', $this->validData([
            'password' => 'password123',
            'password_confirmation' => 'different_password'
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
        $this->get('/register')->assertSee('パスワードと一致しません');
    }

    public function test_registration_success_and_redirect_to_profile_setting()
    {
        $data = $this->validData();

        $response = $this->post('/register', $data);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $response->assertRedirect('/mypage/profile');

        $this->assertAuthenticated();
    }
}
