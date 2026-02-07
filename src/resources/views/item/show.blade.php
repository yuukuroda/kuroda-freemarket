@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="left">
        <!-- 商品画像 -->
        <div class="item_img">
            <img class="img_preview" src="{{'/storage/'.$item['image']}}">
        </div>
    </div>

    <div class="right">
        <!-- 商品名 -->
        <div class="item_name">{{ $item->name }}</div>

        <!-- ブランド名 -->
        <div class="item_brand">{{ $item->brand }}</div>

        <!-- 価格 -->
        <div class="item_price">{{ $item->price }}</div>

        <!-- いいね -->
        @auth
        @if($item->isGoodByAuthUser())
        <!-- いいね解除ボタン -->
        <form action="{{ route('destroy', $item) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="heart_logo"><img src="{{ asset('img/ハートロゴ_ピンク.png') }}" alt="coachtech"> ({{ $item->good->count() }})</button>
        </form>
        @else
        <!-- いいね登録ボタン -->
        <form action="{{ route('store', $item) }}" method="POST">
            @csrf
            <button type="submit" class="heart_logo"><img src="{{ asset('img/ハートロゴ_デフォルト.png') }}" alt="coachtech">({{ $item->good->count() }})</button>
        </form>
        @endif
        @endauth

        <!-- コメント数 -->
        <img src="{{ asset('img/ふきだしロゴ.png') }}" alt="coachtech">

        <!-- 購入手続き -->
        <div class="purchase__button">
            <button class="form__button-submit" type="submit">購入手続きへ</button>
        </div>

        <!-- 商品説明 -->
        <h2>商品説明</h2>

        <!-- 商品情報 -->
        <div class="item_description">{{ $item->description }}</div>

        <!-- カテゴリー -->
        <p>カテゴリー</p>

        <!-- 商品状態 -->
        <p>商品の状態</p>
        <div class="item_condition">{{ $item->condition }}</div>

        <!-- コメント -->
        <p>コメント</p>
        @foreach ($item->comments as $comment)
        <div class="comment__content">
            <span class="comment__user-name">
                {{ Auth::user()->name }}
            </span>
            <div class="comment__item">
                <p>{{ $comment->comment }}</p>
            </div>
        </div>
        @endforeach
        <!-- コメントを送信する -->
        <form action="{{ route('store', ['itemId' => $item->id]) }}" method="POST">
            @csrf
            <p>商品へのコメント</p>
            <input type="text" name="comment">
            <button class="send__button-submit" type="submit">コメントを送信する</button>
        </form>

    </div>
</div>
@endsection