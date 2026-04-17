<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;

class T09_CommentTest extends TestCase
{
    use RefreshDatabase;

    private function prepareData()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'description' => 'テスト説明',
            'price' => 1000,
            'image' => 'test.jpg',
            'condition' => '良好',
        ]);

        $user = User::create([
            'name' => '投稿者',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        return [$user, $item];
    }

    public function test_ログイン済みのユーザーはコメントを送信できる()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->post(route('store', ['itemId' => $item->id]), [
                'comment' => 'これはテストコメントです。'
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'これはテストコメントです。'
        ]);

        $response->assertRedirect(route('item.show', ['itemId' => $item->id]));
    }

    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        $seller = User::create(['name' => 'S', 'email' => 's@e.c', 'password' => 'p', 'email_verified_at' => now()]);
        $item = Item::create(['user_id' => $seller->id, 'name' => 'I', 'description' => 'D', 'price' => 100, 'image' => 'a.j', 'condition' => 'C']);

        $response = $this->post(route('store', ['itemId' => $item->id]), [
            'comment' => 'ゲストコメント'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->from(route('item.show', ['itemId' => $item->id]))
            ->post(route('store', ['itemId' => $item->id]), [
                'comment' => ''
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
    }

    public function test_コメントが255字以上の場合、バリデーションメッセージが表示される()
    {
        [$user, $item] = $this->prepareData();
        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->from(route('item.show', ['itemId' => $item->id]))
            ->post(route('store', ['itemId' => $item->id]), [
                'comment' => $longComment
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
    }
}
