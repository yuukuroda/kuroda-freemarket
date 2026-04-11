@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}?v={{ time() }}">
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
        <div class="right-content1">
            <!-- 商品名 -->
            <div class="item_name">{{ $item->name }}</div>

            <!-- ブランド名 -->
            <div class="item_brand">{{ $item->brand }}</div>

            <!-- 価格 -->
            <div class="item_price">{{ $item->price }}</div>

            <div class="logo_count">
                <!-- いいね -->
                @if($item->isGoodByAuthUser())
                <!-- いいね解除ボタン -->
                <form action="{{ route('destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="heart_logo"><img src="{{ asset('img/ハートロゴ_ピンク.png') }}" alt="coachtech"> <span class="count-num_good">{{ $item->good->count() }}</span></button>
                </form>
                @else
                <!-- いいね登録ボタン -->
                <form action="{{ route('add', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="heart_logo"><img src="{{ asset('img/ハートロゴ_デフォルト.png') }}" alt="coachtech"><span class="count-num_good">{{ $item->good->count() }}</span></button>
                </form>
                @endif

                <!-- コメント数 -->
                <div class="comment_count">
                    <img src="{{ asset('img/ふきだしロゴ.png') }}" alt="coachtech">
                    <span class="count-num_comment">
                        {{ $item->comments->count() }}</span>
                </div>
            </div>
        </div>

        <!-- 購入手続き -->
        <div class="purchase__button">
            <form action="{{route('purchase.create', ['itemId' => $item->id])}}" method="GET">
                <button class="form__button-submit" type="submit">購入手続きへ</button>
            </form>
        </div>

        <div class="right-content2">
            <!-- 商品説明 -->
            <div class="description__title">
                <h2>商品説明</h2>
            </div>
            <!-- 商品情報 -->
            <div class="item_description">{{ $item->description }}</div>
        </div>

        <div class="right-content3">
            <!-- 商品の情報 -->
            <div class="information__title">
                <h2>商品の情報</h2>
            </div>

            <!-- カテゴリー -->
            <div class="category_content">
                <div class="category_title">
                    <span>カテゴリー</span>
                </div>
                <div class="item_category">
                    @foreach($item->categories as $category)
                    <span>
                        {{ $category->content }}
                    </span>
                    @endforeach
                </div>
            </div>

            <!-- 商品状態 -->
            <div class="condition_content">
                <div class="condition_title">
                    <span>商品の状態</span>
                </div>
                <div class="item_condition">{{ $item->condition }}</div>
            </div>
        </div>

        <div class="right-content4">
            <div class="comment__content1">
                <!-- コメント -->
                <div class="comment_title">
                    <span>コメント</span>
                </div>
                <div class="comment_count">
                    ({{ $item->comments->count() }})
                </div>
            </div>

            <div class="comment__content2">
                @foreach ($item->comments as $comment)
                <span class="comment__user-name">
                    {{ $comment->user?->name }}
                </span>
                <div class="comment__item">
                    <p>{{ $comment->comment }}</p>
                </div>
                @endforeach
            </div>

            <!-- コメントを送信する -->
            <form action="{{ route('store', ['itemId' => $item->id]) }}" method="POST">
                @csrf
                <div class="comment_send-title">
                    <p>商品へのコメント</p>
                </div>
                <div class="comment_input-area">
                    <textarea name="comment"></textarea>
                </div>
                <div class="form__error">
                    @error('comments')
                    {{ $message }}
                    @enderror
                </div>
                <button class="send__button-submit" type="submit">コメントを送信する</button>

            </form>
        </div>
    </div>
</div>
@endsection