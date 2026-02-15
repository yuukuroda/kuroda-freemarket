@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<!-- プロフィール設定 -->
<h1>プロフィール設定</h1>

<form class="form" action="/mypage/profile/update" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    <!-- 画像 -->
    <img class="img_preview" src="{{'/storage/'.$profile['image']}}">
    <input type="hidden" name="image" value="{{ $profile['image'] }}">
    <div class="form__input--image">
        <input type="file" name="image"
            onchange="previewImage(this)" placeholder="画像を選択する" />
    </div>
    <!-- ユーザー名 -->
    <div class="content__title">ユーザー名</div>
    <div class="form__input--text">
        <input type="text" name="name" value="{{ old('name', $profile->name ?? Auth::user()->name) }}" />
    </div>
    <!-- 郵便番号 -->
    <div class="content__title">郵便番号</div>
    <div class="form__input--text">
        <input type="text" name="post_code" value="{{ old('post_code',$profile->post_code) }}" />
    </div>
    <!-- 住所 -->
    <div class="content__title">住所</div>
    <div class="form__input--text">
        <input type="text" name="address" value="{{ old('address',$profile->address) }}" />
    </div>
    <!-- 建物名 -->
    <div class="content__title">建物名</div>
    <div class="form__input--text">
        <input type="text" name="building" value="{{ old('building',$profile->building) }}" />
    </div>
    <!-- 更新する -->
    <button class="form__button-submit submit__button" type="submit">更新する</button>
</form>
@endsection