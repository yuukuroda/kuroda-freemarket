<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Good;

class T08_GoodTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function createItem()
    {
        /** @var User $seller */
        $seller = User::factory()->create();

        return Item::create([
            'user_id' => $seller->id,
            'image' => 'test.jpg',
            'condition' => '良好',
            'name' => 'テスト商品',
            'description' => 'テスト説明文',
            'price' => 1000,
        ]);
    }

    public function test_いいねアイコンを押下することによって、いいねした商品として登録することができる。()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = $this->createItem();

        $response = $this->actingAs($user)->post(route('add', ['itemId' => $item->id]));

        $response->assertStatus(302);

        $this->assertDatabaseHas('goods', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)->get('/item/' . $item->id)
            ->assertStatus(200)
            ->assertSee('1');
    }

    public function test_追加済みのアイコンは色が変化する()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = $this->createItem();

        Good::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('ハートロゴ_ピンク.png');
    }

    public function test_再度いいねアイコンを押下することによって、いいねを解除することができる。()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = $this->createItem();

        Good::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->delete(route('destroy', ['itemId' => $item->id]));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('goods', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)->get('/item/' . $item->id)
            ->assertStatus(200)
            ->assertSee('0');
    }
}
