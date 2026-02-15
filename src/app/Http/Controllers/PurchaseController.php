<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function create($itemId)
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);;
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
}
