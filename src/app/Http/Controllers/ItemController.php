<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Good;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');

        if ($tab === 'mylist') {
            if (Auth::check()) {
                $query = Item::whereHas('good', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            } else {
                $query = Item::whereRaw('1 = 0');
            }
        } else {
            $query = Item::query();
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->latest()->get();

        return view('item.index', compact('items', 'tab'));
    }

    public function show($itemId)
    {
        $item = Item::With('categories', 'comments.user', 'good')->findOrFail($itemId);
        $categories = Category::all();
        $good = Good::all();
        return view('item.show', compact('item', 'categories',));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $item = Item::firstOrNew(['user_id' => $user->id]);
        $categories = Category::all();

        return view('item.create', compact('user', 'item', 'categories'));
    }

    public function store(ItemRequest $request)
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

        return redirect()->route('profile.index', ['tab' => 'sell'])->with('message', '商品の出品が完了しました。');
    }

    public function search(Request $request)
    {

        $query = Item::query();
        if (!empty($request->keyword)) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        };
        $items = $query->get();
        return view('item.index', compact('items'));
    }
}
