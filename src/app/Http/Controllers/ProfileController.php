<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function create()
    {
        return view('profile.create');
    }

    // public function store(Request $request)
    // {
    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('profiles', 'public');
    //     }

    //     Profile::create([
    //         'user_id' => Auth::id(),
    //         'image' => $imagePath,
    //         'name' => $request->name,
    //         'post_code' => $request->post_code,
    //         'address' => $request->address,
    //         'building' => $request->building,
    //     ]);

    //     return redirect()->route('item.index');
    // }

    public function update(Request $request)
    {
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

        return redirect()->route('item.index');
    }
}
