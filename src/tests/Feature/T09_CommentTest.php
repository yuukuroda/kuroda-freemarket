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

    /**
     * テスト用データの準備
     */
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
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        return [$user, $item];
    }

    /**
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_ログイン済みのユーザーはコメントを送信できる()
    {
        [$user, $item] = $this->prepareData();

        // 解決策: withoutMiddleware(['verified']) を使用して
        // メール認証チェックのみをバイパスし、authミドルウェアは生かします。
        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->post(route('store', ['itemId' => $item->id]), [
                'comment' => 'これはテストコメントです。'
            ]);

        // データベースに保存されているか確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'これはテストコメントです。'
        ]);

        // 商品詳細画面へ戻るか確認
        $response->assertRedirect(route('item.show', ['itemId' => $item->id]));
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        $seller = User::create(['name' => 'S', 'email' => 's@e.c', 'password' => 'p', 'email_verified_at' => now()]);
        $item = Item::create(['user_id' => $seller->id, 'name' => 'I', 'description' => 'D', 'price' => 100, 'image' => 'a.j', 'condition' => 'C']);

        // ゲストはあえてミドルウェアを有効にしてリダイレクトを確認
        $response = $this->post(route('store', ['itemId' => $item->id]), [
            'comment' => 'ゲストコメント'
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * バリデーション：未入力
     */
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

    /**
     * バリデーション：最大文字数
     */
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
