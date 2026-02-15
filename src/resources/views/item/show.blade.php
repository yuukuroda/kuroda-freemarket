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
        @if($item->isGoodByAuthUser())
        <!-- いいね解除ボタン -->
        <form action="{{ route('destroy', $item->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="heart_logo"><img src="{{ asset('img/ハートロゴ_ピンク.png') }}" alt="coachtech"> ({{ $item->good->count() }})</button>
        </form>
        @else
        <!-- いいね登録ボタン -->
        <form action="{{ route('add', $item->id) }}" method="POST">
            @csrf
            <button type="submit" class="heart_logo"><img src="{{ asset('img/ハートロゴ_デフォルト.png') }}" alt="coachtech">({{ $item->good->count() }})</button>
        </form>
        @endif

        <!-- コメント数 -->
        <img src="{{ asset('img/ふきだしロゴ.png') }}" alt="coachtech">
        ({{ $item->comments->count() }})

        <!-- 購入手続き -->
        <form action="{{route('purchase.create', ['itemId' => $item->id])}}" method="GET">
            <!-- <form action="/purchase/{item_id}" method="get"> -->
            <div class="purchase__button">
                <button class="form__button-submit" type="submit">購入手続きへ</button>
            </div>
        </form>

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
        <p>コメント</p>({{ $item->comments->count() }})
        @foreach ($item->comments as $comment)
        <div class="comment__content">
            <span class="comment__user-name">
                {{ $comment->user?->name }}
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