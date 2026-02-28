<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    public function create($itemId)
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);
        $addressData = session('new_address', [
            'post_code' => $user->profile->post_code,
            'address'   => $user->profile->address,
            'building'  => $user->profile->building,
        ]);

        return view('purchase.create', compact('item', 'addressData'));
    }

    public function address($itemId)
    {
        $item = Item::findOrFail($itemId);
        $user = Auth::user();
        $addressData = session('new_address', [
            'post_code' => $user->profile->post_code,
            'address'   => $user->profile->address,
            'building'  => $user->profile->building,
        ]);

        return view('purchase.address', compact('item', 'addressData'));
    }

    public function update(Request $request, $itemId)
    {
        session(['new_address' => [
            'post_code' => $request->post_code,
            'address'   => $request->address,
            'building'  => $request->building,
        ]]);

        return redirect()->route('purchase.create', ['itemId' => $itemId]);
    }

    public function store(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);
        $user = Auth::user();
        
        $addressData = session('new_address', [
            'post_code' => $user->profile->post_code ?? '',
            'address'   => $user->profile->address ?? '',
            'building'  => $user->profile->building ?? '',
        ]);

        if ($request->payment_method === 'konbini') {
            DB::transaction(function () use ($itemId, $addressData) {
                Purchase::create([
                    'user_id'   => Auth::id(),
                    'item_id'   => $itemId,
                    'post_code' => $addressData['post_code'],
                    'address'   => $addressData['address'],
                    'building'  => $addressData['building'],
                    'payment'   => 'コンビニ支払い',
                ]);
                // セッション削除
                session()->forget('new_address');
            });
        }
        // Stripeのシークレットキーをセット
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $successUrl = ($request->payment_method === 'card')
            ? route('purchase.success', ['itemId' => $item->id])
            : route('purchase.konbini_complete', ['itemId' => $item->id]);

        // Stripe Checkoutセッションの作成
        $session = Session::create([
            'payment_method_types' => [$request->payment_method], // 'card' or 'konbini'
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // 決済成功時のリダイレクト先
            'success_url' => route('purchase.success', ['itemId' => $item->id]),
            // 決済キャンセル時のリダイレクト先
            'cancel_url' => route('purchase.create', ['itemId' => $item->id]),
        ]);

        // Stripeの決済画面へリダイレクト
        return redirect($session->url);
    }

    public function success(Request $request, $itemId)
    {
        $user = Auth::user();

        // 1. 保存するための住所データを取得（セッションまたはプロフィールから）
        $addressData = session('new_address', [
            'post_code' => $user->profile->post_code ?? '',
            'address'   => $user->profile->address ?? '',
            'building'  => $user->profile->building ?? '',
        ]);
        // データベースに購入情報を保存
        DB::transaction(function () use ($itemId, $addressData) {
            Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'post_code' => $addressData['post_code'],
                'address'   => $addressData['address'],
                'building'  => $addressData['building'],
                // カード支払いの成功時にここに来るため、固定で 'card' または 
                // 必要に応じて Stripe セッションから取得した値を入れます
                'payment' => 'カード支払い',
            ]);

            // 商品の状態を「売り切れ」に更新する場合などの処理をここに書く
        });

        // 商品一覧ページへリダイレクトし、メッセージを表示
        return redirect('/');
    }

    public function konbiniComplete(Request $request, $itemId)
    {
        $user = Auth::user();
        $addressData = session('new_address', [
            'post_code' => $user->profile->post_code ?? '',
            'address'   => $user->profile->address ?? '',
            'building'  => $user->profile->building ?? '',
        ]);

        // コンビニ払いの情報をDBに保存
        DB::transaction(function () use ($itemId, $addressData) {
            \App\Models\Purchase::create([
                'user_id'   => Auth::id(),
                'item_id'   => $itemId,
                'post_code' => $addressData['post_code'],
                'address'   => $addressData['address'],
                'building'  => $addressData['building'],
                'payment'   => 'コンビニ支払い', // コンビニ支払いを記録
            ]);

            session()->forget('new_address');
        });

        // 保存後、案内画面を表示（または一覧へ戻す）
        return view('purchase.konbini_notified', ['itemId' => $itemId]);
    }
}
