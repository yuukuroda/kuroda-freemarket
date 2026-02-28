@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/index.css') }}">
@endsection

@section('content')
<!-- ヘッダー -->

<!-- おすすめ -->
<a href="{{ url('/') }}">おすすめ</a>
<!-- <span id="tab-recommend" class="header-nav" style="cursor: pointer;">おすすめ</span> -->
<!-- マイリスト -->
<a href="{{ url('/?tab=mylist') }}"
    class="header-nav {{ request('tab') === 'mylist' ? 'active' : '' }}">
    マイリスト
</a>
<!-- <span id="tab-mylist" class="header-nav" style="cursor: pointer;">マイリスト</span> -->


<!-- 本文 -->

<!-- 商品 -->
@foreach ($items as $item)
<a href="{{url('/item/' . $item->id)}}" class="show__link @if($item->good) is-good @endif">
    <div class="product__display">
        <!-- sold -->
        @if($item->purchase()->exists())
        <div class="sold-badge">
            <span>Sold</span>
        </div>
        @endif
        <!-- 画像 -->
        <div class="item__img"><img class="img_preview" src="{{'/storage/'.$item['image']}}">
            <input type="hidden" name="image" value="{{ $item['image'] }}">
        </div>
        <!-- 名前 -->
        <div class="item_name">{{ $item->name }}</div>
    </div>
    @endforeach

    @endsection