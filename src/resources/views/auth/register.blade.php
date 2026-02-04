@extends('auth.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')

<form class="form" action="/register" method="post" novalidate>
    @csrf
    <!-- 会員登録 -->
    <h1>会員登録</h1>
    <!-- ユーザー名 -->
    <span class="login-form__title">ユーザー名</span>
    <div class="user-form__input--text">
        <input type="text" name="name" value="{{ old('name') }}" />
    </div>
    <div class="form__error">
        @error('name')
        {{ $message }}
        @enderror
    </div>
    <!-- メールアドレス -->
    <span class="login-form__title">メールアドレス</span>
    <div class="login-form__input--text">
        <input type="email" name="email" value="{{ old('email') }}" />
    </div>
    <div class="form__error">
        @error('email')
        {{ $message }}
        @enderror
    </div>
    <!-- パスワード -->
    <span class="login-form__title">パスワード</span>
    <div class="login-form__input--text">
        <input type="password" name="password" value="{{ old('password') }}" />
    </div>
    <div class="form__error">
        @error('password')
        {{ $message }}
        @enderror
    </div>
    <!-- 確認用パスワード -->
    <span class="login-form__title">確認用パスワード</span>
    <div class="login-form__input--text">
        <input type="password" name="password_confirmation" value="{{ old('password') }}" />
    </div>
    @error('password_confirmation')
    {{ $message }}
    @enderror
    <!-- 登録する -->
    <div class="form__button">
        <button class="form__button-submit" type="submit">登録する</button>
    </div>
    <!-- ログインはこちら -->
    <a class="header-nav__link" href="{{ url('/login') }}">ログインはこちら</a>
</form>
@endsection