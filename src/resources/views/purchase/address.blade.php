@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}">
@endsection

@section('content')
<!-- 住所の変更 -->
<h1>住所の変更</h1>

<form class="form" action="{{ route('purchase.address.update', ['itemId' => $item->id]) }}" method="POST" novalidate>
    @csrf
    <!-- 郵便番号 -->
    <div class="content__title">郵便番号</div>
    <div class="form__input--text">
        <input type="text" name="post_code" value="{{ old('post_code') }}" />
    </div>
    <!-- 住所 -->
    <div class="content__title">住所</div>
    <div class="form__input--text">
        <input type="text" name="address" value="{{ old('address') }}" />
    </div>
    <!-- 建物名 -->
    <div class="content__title">建物名</div>
    <div class="form__input--text">
        <input type="text" name="building" value="{{ old('building') }}" />
    </div>
    <!-- 更新する -->
    <button class="form__button-submit submit__button" type="submit">更新する</button>
</form>
@endsection