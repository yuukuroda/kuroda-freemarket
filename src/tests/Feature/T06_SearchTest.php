<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Good;

class T06_SearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_「商品名」で部分一致検索ができる()
    {
        $seller = User::create([
            'name' => 'Seller',
            'email' => 'seller_' . uniqid() . '@example.com',
            'password' => 'pass'
        ]);

        // 検索にヒットする商品
        Item::create([
            'user_id' => $seller->id,
            'image' => 'match.jpg',
            'condition' => '良好',
            'name' => 'プログラミング入門',
            'description' => 'PHPの本',
            'price' => 2000,
        ]);

        // 検索にヒットしない商品
        Item::create([
            'user_id' => $seller->id,
            'image' => 'unmatch.jpg',
            'condition' => '良好',
            'name' => '料理の基本',
            'description' => '和食の本',
            'price' => 1500,
        ]);

        // ビューの input name="keyword" に合わせてリクエストを送る
        $response = $this->get('/?keyword=プロ');

        $response->assertStatus(200);

        // 「プログラミング入門」は表示され、「料理の基本」は表示されないことを検証
        $response->assertSee('プログラミング入門');
        $response->assertDontSee('料理の基本');
    }

    /**
     * 検索状態がマイリストでも保持されている
     * 1. ホームページで商品を検索
     * 2. 検索結果が表示される
     * 3. マイリストページに遷移
     * 期待: 検索キーワードが保持されている
     */
    public function test_検索状態がマイリストでも保持されている()
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user_' . uniqid() . '@example.com',
            'password' => 'pass'
        ]);
        $seller = User::create(['name' => 'Seller', 'email' => 's_' . uniqid() . '@example.com', 'password' => 'pass']);

        $matchItem = Item::create([
            'user_id' => $seller->id,
            'image' => 'item1.jpg',
            'condition' => '良好',
            'name' => '検索対象商品',
            'description' => '説明',
            'price' => 1000,
        ]);

        // いいねしておく
        Good::create(['user_id' => $user->id, 'item_id' => $matchItem->id]);

        // 1. ホームページで検索 (keywordを使用)
        $response = $this->actingAs($user)->get('/?keyword=検索対象');
        $response->assertSee('検索対象商品');

        // 2. マイリストタブへのリンクに検索ワードが含まれているか確認
        // ビューのリンク生成ロジックが正しい場合、現在のクエリパラメータが引き継がれるはずです
        // 実際の遷移をシミュレート
        $response = $this->actingAs($user)->get('/?keyword=検索対象&tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('検索対象商品');

        // ページ内のリンクにキーワードが含まれているか検証
        // 前回の失敗ログを見るとビューでは ?name= を使っているようなので、
        // アプリの実装が keyword と name どちらを使っているかによってここを調整してください。
        // ここでは keyword に統一する想定です。
        $response->assertSee('keyword=%E6%A4%9C%E7%B4%A2%E5%AF%BE%E8%B1%A1');
    }
}
