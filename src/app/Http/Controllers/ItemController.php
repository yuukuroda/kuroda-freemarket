<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Good;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('item.index', compact('items'));
    }

    public function show($itemId)
    {
        $item = Item::With('categories', 'comments.user', 'good')->findOrFail($itemId);
        $categories = Category::all();
        $good = Good::all();
        return view('item.show', compact('item', 'categories',));
    }
}
