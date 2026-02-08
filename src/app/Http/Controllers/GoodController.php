<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class GoodController extends Controller
{
    public function add(Item $item)
    {
        $item->good()->create(['user_id' => auth()->id()]);
        return back(); 
    }

    public function destroy(Item $item)
    {
        $item->good()->where('user_id', auth()->id())->delete();
        return back();
    }
}
