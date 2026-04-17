@extends('auth.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}?v={{ time() }}">
@endsection

@section('content')

@if (session('message'))
<div class="alert">
    {{ session('message') }}
</div>
@endif

<p>
    登録していただいたメールアドレスに認証メールを送付しました。<br>
    メール認証を完了してください。
</p>

<div class="button-container">
    <a href="http://localhost:8025/" class="link-button">
        認証はこちらから
    </a>
</div>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="btn-resend">
        認証メールを再送する
    </button>
</form>
@endsection