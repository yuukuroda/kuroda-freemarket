<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $itemId)
    {
        Comment::create([
            'user_id'    => Auth::id(),
            'item_id' => $itemId,
            'comment'    => $request->comment,
        ]);

        return redirect()->route('items.show', ['itemId' => $itemId]);
    }
}
