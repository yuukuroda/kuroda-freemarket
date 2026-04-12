<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        $comment = new \App\Models\Comment();

        $comment->user_id = \Illuminate\Support\Facades\Auth::id();
        $comment->item_id = $itemId;
        $comment->comment = $request->comment;
        $comment->save();

        return redirect()->route('item.show', ['itemId' => $itemId]);
    }
}
