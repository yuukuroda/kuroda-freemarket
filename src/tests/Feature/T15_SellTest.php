<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class T15_SellTest extends TestCase
{
    use RefreshDatabase;

    private function prepareData()
    {
        $user = User::create([
            'name' => '出品ユーザー',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $category1 = Category::create(['content' => 'ファッション']);
        $category2 = Category::create(['content' => 'レディース']);

        return [$user, $category1, $category2];
    }

    public function test_商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）()
    {
        [$user, $category1, $category2] = $this->prepareData();

        Storage::fake('public');

        $image = UploadedFile::fake()->create('item_test.jpg', 100, 'image/jpeg');

        $itemData = [
            'name'         => 'テスト商品名',
            'brand'        => 'テストブランド',
            'description'  => '商品の詳細説明テキストです。',
            'price'        => 5000,
            'condition'    => 'good',
            'image'        => $image,
            'categories'   => [$category1->id, $category2->id],
        ];

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->post(route('sell.store'), $itemData);

        $response->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'user_id'     => $user->id,
            'name'        => $itemData['name'],
        ]);

        $item = Item::where('name', $itemData['name'])->first();
        $this->assertNotNull($item);

        $savedPath = $item->image;
        $this->assertNotEmpty($savedPath);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        $storage->assertExists($savedPath);

        $this->assertTrue($item->categories->contains($category1->id));
        $this->assertTrue($item->categories->contains($category2->id));
    }
}
