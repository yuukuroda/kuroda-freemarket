<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        return view('profile.show', compact('user','profile'));
    }

    public function update(ProfileRequest $request)
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
            return redirect()->route('item.index');
        } else {
            return redirect()->route('profile.index');
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        if ($page === 'buy') {
            $items = Purchase::where('user_id', $user->id)->with('item')->latest()->get()->pluck('item');
        } else {
            $items = Item::where('user_id', $user->id)->latest()->get();
        }

        return view('profile.index', compact('items', 'page'));
    }
}
