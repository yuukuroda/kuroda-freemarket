<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function create($itemId)
    {
        $item = Item::findOrFail($itemId);;
        
        return view('purchase.create', compact('item'));
    }
}
