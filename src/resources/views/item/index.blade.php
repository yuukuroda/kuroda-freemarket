@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/index.css') }}">
@endsection

@section('content')
<!-- ヘッダー -->

<!-- おすすめ -->
<span class="header-nav">おすすめ</span>
<!-- マイリスト -->
<span class="header-nav">マイリスト</span>


<!-- 本文 -->

<!-- 商品 -->
@foreach ($items as $item)
<a href="{{url('/item/' . $item->id)}}" class="show__link">
    <div class="product__display">
        <!-- 画像 -->
        <div class="item__img"><img class="img_preview" src="{{'/storage/'.$item['image']}}">
            <input type="hidden" name="image" value="{{ $item['image'] }}">
        </div>
        <!-- 名前 -->
        <div class="item_name">{{ $item->name }}</div>
    </div>
    @endforeach

    @endsection