<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

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

    public function update(AddressRequest $request, $itemId)
    {
        session(['new_address' => [
            'post_code' => $request->post_code,
            'address'   => $request->address,
            'building'  => $request->building,
        ]]);

        return redirect()->route('purchase.create', ['itemId' => $itemId]);
    }

    public function store(PurchaseRequest $request, $itemId)
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
                session()->forget('new_address');
            });
        }
      
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $successUrl = ($request->payment_method === 'card')
            ? route('purchase.success', ['itemId' => $item->id])
            : route('purchase.konbini_complete', ['itemId' => $item->id]);

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
            'success_url' => route('purchase.success', ['itemId' => $item->id]),
            'cancel_url' => route('purchase.create', ['itemId' => $item->id]),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, $itemId)
    {
        $user = Auth::user();

        $addressData = session('new_address', [
            'post_code' => $user->profile->post_code ?? '',
            'address'   => $user->profile->address ?? '',
            'building'  => $user->profile->building ?? '',
        ]);
        DB::transaction(function () use ($itemId, $addressData) {
            Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'post_code' => $addressData['post_code'],
                'address'   => $addressData['address'],
                'building'  => $addressData['building'],
                'payment' => 'カード支払い',
            ]);
        });

        return redirect('/')->with('message', '商品の購入が完了しました。');
    }

    public function konbiniComplete(Request $request, $itemId)
    {
        $user = Auth::user();
        $addressData = session('new_address', [
            'post_code' => $user->profile->post_code ?? '',
            'address'   => $user->profile->address ?? '',
            'building'  => $user->profile->building ?? '',
        ]);

        DB::transaction(function () use ($itemId, $addressData) {
            \App\Models\Purchase::create([
                'user_id'   => Auth::id(),
                'item_id'   => $itemId,
                'post_code' => $addressData['post_code'],
                'address'   => $addressData['address'],
                'building'  => $addressData['building'],
                'payment'   => 'コンビニ支払い', 
            ]);

            session()->forget('new_address');
        });

        return view('purchase.konbini_notified', ['itemId' => $itemId]);
    }
}
