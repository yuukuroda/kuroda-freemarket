@extends('auth.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="content">
    <form class="form" action="/login" method="post" novalidate>
        @csrf
        <!-- ログイン -->
        <h1>ログイン</h1>
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

        <!-- ログインする -->
        <div class="form__button">
            <button class="form__button-submit" type="submit">ログインする</button>
        </div>
        <!-- 会員登録はこちら -->
        <a class="header-nav__link" href="{{ url('/register') }}">会員登録はこちら</a>
    </form>
</div>
@endsection