<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class T03_LogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_ログアウトができる()
    {
        $user = User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => bcrypt('password')]);

        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $response->assertStatus(302);

        $this->assertGuest();
    }
}
