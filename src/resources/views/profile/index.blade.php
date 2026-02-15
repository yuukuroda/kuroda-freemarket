@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="top">
        <!-- ユーザー画像 -->
        <img src="{{ asset('storage/' . Auth::user()->profile->image) }}"
            alt="プロフィール画像"
            style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
        <!-- ユーザー名 -->
        <div class="header-nav__user-name">
            {{ Auth::user()->profile?->name }}
        </div>
        <!-- プロフィールを編集 -->
        <a class="header-nav__link" href="{{ url('/mypage/profile') }}">プロフィールを編集</a>
    </div>

    <div class="bottom">

    </div>

</div>
@endsection