<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class T09_CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /**
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_logged_in_user_can_send_comment()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $commentData = [
            'comment' => 'これはテストコメントです。'
        ];

        // ログインしてコメントを投稿
        // ルート名を 'comment.store' に修正（もしアプリ側が違う名前ならここを合わせます）
        $response = $this->actingAs($user)
            ->post("/item/{$item->id}/comment", $commentData);

        // 判定: リダイレクトされること
        $response->assertStatus(302);

        // 判定: データベースに保存されていること
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'これはテストコメントです。'
        ]);
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_send_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'ゲストのコメント'
        ]);

        // 判定: ログイン画面へリダイレクトされること
        $response->assertRedirect('/login');
    }

    /**
     * コメントが空の場合バリデーションメッセージが表示される
     */
    public function test_comment_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->from("/item/{$item->id}") // 遷移元を明示
            ->post("/item/{$item->id}/comment", [
                'comment' => ''
            ]);

        // 判定: バリデーションエラーが返ってくること
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * コメントが255文字を超える場合バリデーションメッセージが表示される
     */
    public function test_comment_max_length()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->from("/item/{$item->id}")
            ->post("/item/{$item->id}/comment", [
                'comment' => $longComment
            ]);

        // 判定: バリデーションエラーが返ってくること
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
    }
}
