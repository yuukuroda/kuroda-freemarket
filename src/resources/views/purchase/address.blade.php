@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="content">

    <form class="form" action="{{ route('purchase.address.update', ['itemId' => $item->id]) }}" method="POST" novalidate>
        @csrf
        <!-- 住所の変更 -->
        <h1>住所の変更</h1>
        <!-- 郵便番号 -->
        <div class="content__title">郵便番号</div>
        <div class="form__input--text">
            <input type="text" name="post_code" value="{{ old('post_code') }}" />
        </div>
        <div class="form__error">
            @error('post_code')
            {{ $message }}
            @enderror
        </div>

        <!-- 住所 -->
        <div class="content__title">住所</div>
        <div class="form__input--text">
            <input type="text" name="address" value="{{ old('address') }}" />
        </div>
        <div class="form__error">
            @error('address')
            {{ $message }}
            @enderror
        </div>

        <!-- 建物名 -->
        <div class="content__title">建物名</div>
        <div class="form__input--text">
            <input type="text" name="building" value="{{ old('building') }}" />
        </div>
        <!-- 更新する -->
        <button class="form__button-submit submit__button" type="submit">更新する</button>

        <!-- 購入手続き画面へ戻る -->
        <div class="back-link-container">
            <a href="{{ route('purchase.create', ['itemId' => $item->id]) }}" class="back-link">
                購入手続き画面へ戻る
            </a>
        </div>
    </form>
</div>
@endsection