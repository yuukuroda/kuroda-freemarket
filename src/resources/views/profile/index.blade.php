@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}?v={{ time() }}">
@endsection

@section('content')
@if (session('message'))
<div class="alert alert-success">
    {{ session('message') }}
</div>
@endif
<div class="content">
    <div class="top">
        <!-- ユーザー画像 -->
        <div class="profile_img">
            <img src="{{ asset('storage/' . Auth::user()->profile?->image) }}"
                alt="プロフィール画像">
        </div>
        <!-- ユーザー名 -->
        <div class="header-nav__user-name">
            {{ Auth::user()->profile?->name }}
        </div>
        <!-- プロフィールを編集 -->
        <a class="header-nav__profile" href="{{ url('/mypage/profile') }}">プロフィールを編集</a>
    </div>

    <div class="bottom">
        <div class="sub-header">
            <!-- 出品した商品 -->
            <a href="{{ url('/mypage?page=sell') }}" class="header-nav tab-sale">
                出品した商品
            </a>
            <!-- 購入した商品 -->
            <a href="{{ url('/mypage?page=buy') }}" class="header-nav tab-purchase">
                購入した商品
            </a>
        </div>

        <div class="product__grid">
            @foreach($items as $item)
            <div class="show__link">
                <div class="product__display">
                    <div class="item__img"><img class="img_preview" src="{{'/storage/'.$item['image']}}">
                        <input type="hidden" name="image" value="{{ $item['image'] }}">
                    </div>
                    <!-- 名前 -->
                    <div class="item_name">{{ $item->name }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection