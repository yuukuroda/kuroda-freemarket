@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/index.css') }}?v={{ time() }}">
@endsection

@section('content')
<!-- ヘッダー -->
<div class="sub-header">
    <!-- おすすめ -->
    <a href="{{ url('/') . '?' . http_build_query(array_merge(request()->query(), ['tab' => null])) }}"
        class="header-nav tab-recommend {{ request('tab') !== 'mylist' ? 'active' : '' }}">
        おすすめ
    </a>

    <!-- マイリスト -->
    <a href="{{ url('/') . '?' . http_build_query(array_merge(request()->query(), ['tab' => 'mylist'])) }}"
        class="header-nav tab-mylist {{ request('tab') === 'mylist' ? 'active' : '' }}">
        マイリスト
    </a>
</div>

<!-- 本文 -->

<!-- 商品 -->

<div class="product__grid">
    @foreach ($items as $item)
    <a href="{{url('/item/' . $item->id)}}" class="show__link @if($item->good) is-good @endif">
        <div class="product__display">
            <!-- 画像 -->
            <div class="item__img">
                <!-- sold -->
                @if($item->purchase()->exists())
                <div class="sold-badge">
                    <span>Sold</span>
                </div>
                @endif
                <img class="img_preview" src="{{'/storage/'.$item['image']}}">
                <input type="hidden" name="image" value="{{ $item['image'] }}">
            </div>
            <!-- 名前 -->
            <div class="item_name">{{ $item->name }}</div>
        </div>
    </a>
    @endforeach
</div>
@endsection