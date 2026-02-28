<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        return view('profile.show', compact('user','profile'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $profile = Profile::where('user_id', $userId)->first();
        $isFirstTime = is_null($profile);
        $imagePath = $profile->image ?? null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
        }

        Profile::updateOrCreate(
            [
                'user_id' => Auth::id()
            ],
            [
                'image' => $imagePath,
                'name' => $request->name,
                'post_code' => $request->post_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );
        if ($isFirstTime) {
            // 初回設定時：商品一覧へ
            return redirect()->route('item.index');
        } else {
            // 編集時：マイページへ
            return redirect()->route('profile.index');
        }
        // return redirect()->route('item.index');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        // クレリパラメータ 'page' を取得。デフォルトは 'sell'
        $page = $request->query('page', 'sell');

        if ($page === 'buy') {
            // 購入した商品を取得（Purchaseモデル経由など）
            $items = Purchase::where('user_id', $user->id)->with('item')->get()->pluck('item');
        } else {
            // 出品した商品を取得（Itemモデルで自分のユーザーIDのもの）
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('profile.index', compact('items', 'page'));
    }

    // public function store(Request $request)
    // {
    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('profiles', 'public');
    //     }

    //     Profile::create([
    //         'user_id' => Auth::id(),
    //         'image' => $imagePath,
    //         'name' => $request->name,
    //         'post_code' => $request->post_code,
    //         'address' => $request->address,
    //         'building' => $request->building,
    //     ]);

    //     return redirect()->route('item.index');
    // }
}
