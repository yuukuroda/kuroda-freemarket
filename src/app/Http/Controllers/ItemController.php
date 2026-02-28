<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Good;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');

        if ($tab === 'mylist') {
            if (Auth::check()) {
                $items = Item::whereHas('good', function ($query) {
                    $query->where('user_id', Auth::id());
                })->get();
            } else {
                $items = collect();
            }
        } else {
            $query = Item::query();
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
            $items = $query->get();
        }

        return view('item.index', compact('items', 'tab'));
    }

    public function show($itemId)
    {
        $item = Item::With('categories', 'comments.user', 'good')->findOrFail($itemId);
        $categories = Category::all();
        $good = Good::all();
        return view('item.show', compact('item', 'categories',));
    }

    public function create()
    {
        $user = Auth::user();
        $item = Item::firstOrNew(['user_id' => $user->id]);
        $categories = Category::all();

        return view('item.create', compact('user', 'item', 'categories'));
    }

    public function store(Request $request)
    {
        $itemData = $request->only(['condition', 'name', 'brand', 'description', 'price']);
        $itemData['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $imagePath = $request->image->store('img', 'public');
            $itemData['image'] = $imagePath;
        }

        $item = Item::create($itemData);

        if ($request->has('categories')) {
            $item->categories()->sync($request->categories);
        }

        return redirect('/');
    }
}
