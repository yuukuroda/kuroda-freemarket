<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
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
    
    // Stripeのシークレットキーを設定（envから読み込み）
    Stripe::setApiKey(config('stripe.secret_key'));

    // Stripe Checkoutセッションの作成
    $checkout_session = Session::create([
        'payment_method_types' => ['card'],
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
        'success_url' => route('purchase.success', ['itemId' => $itemId]), // 成功時の戻り先
        'cancel_url' => route('purchase.create', ['itemId' => $itemId]),  // キャンセル時の戻り先
    ]);

    // Stripeの決済画面へリダイレクト
    return redirect($checkout_session->url);
}
}
