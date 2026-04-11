<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Good;

class T07_InformationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_必要な情報が表示される()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $commenter = User::create([
            'name' => 'コメントユーザー',
            'email' => 'commenter_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'image' => 'detail_test.jpg',
            'condition' => '新品同様',
            'name' => '詳細テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これは商品の詳細説明です。',
            'price' => 5000,
        ]);

        $category1 = Category::create(['content' => 'ファッション']);
        $category2 = Category::create(['content' => 'メンズ']);
        $item->categories()->attach([$category1->id, $category2->id]);

        Good::create([
            'user_id' => $seller->id,
            'item_id' => $item->id
        ]);

        $comment = new Comment();
        $comment->user_id = $commenter->id;
        $comment->item_id = $item->id;
        $comment->comment = '素晴らしい商品ですね！';
        $comment->save();

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);

        $response->assertSee('detail_test.jpg');
        $response->assertSee('詳細テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('5000');
        $response->assertSee('これは商品の詳細説明です。');
        $response->assertSee('新品同様');

        $response->assertSee('ファッション');
        $response->assertSee('メンズ');

        $response->assertSee('コメントユーザー');
        $response->assertSee('素晴らしい商品ですね！');
    }

    public function test_複数選択されたカテゴリが表示されているか()
    {
        $seller = User::create([
            'name' => 'Seller',
            'email' => 's_' . uniqid() . '@example.com',
            'password' => 'pass'
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'image' => 'cat_test.jpg',
            'condition' => '良好',
            'name' => '複数カテゴリ商品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $catA = Category::create(['content' => '家電']);
        $catB = Category::create(['content' => 'スマートフォン']);
        $catC = Category::create(['content' => 'Apple']);

        $item->categories()->attach([$catA->id, $catB->id, $catC->id]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('家電');
        $response->assertSee('スマートフォン');
        $response->assertSee('Apple');
    }
}
