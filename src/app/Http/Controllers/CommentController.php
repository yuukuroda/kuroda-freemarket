<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $itemId)
    {
        $comment = new \App\Models\Comment();

        $comment->user_id = \Illuminate\Support\Facades\Auth::id();
        $comment->item_id = $itemId; // ここで引数の $itemId を確実に代入します
        $comment->comment = $request->comment;

        // 3. データベースへ保存実行
        $comment->save();
        // Comment::create([
        //     'user_id' => Auth::id(),
        //     'item_id' => $itemId,
        //     'comment' => $request->comment,
        // ]);

        return redirect()->route('item.show', ['itemId' => $itemId]);
    }
}
