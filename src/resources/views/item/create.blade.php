@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/create.css') }}">
@endsection

@section('content')
<!-- 商品の出品 -->
<h1>商品の出品</h1>

<form class="form" action="{{ route('sell.store')}}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- 商品画像 -->
    <div class="content__title">商品画像</div>
    <div class="form__input--image">
        <label class="image-label">
            <input type="file" name="image"
                onchange="previewImage(this)" placeholder="画像を選択する" />
        </label>
    </div>
    <div class="form__error">
        @error('image')
        {{ $message }}
        @enderror
    </div>

    <!-- 商品の詳細 -->
    <h2>商品の詳細</h2>

    <!-- カテゴリー -->
    <div class="content__title">カテゴリー</div>
    <div class="category-container">
        @foreach($categories as $category)
        <div class="category_selection">
            <input type="checkbox" name="categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}">
            <label for="category_{{ $category->id }}">
                {{ $category->content }}
            </label>
        </div>
        @endforeach
    </div>
    <div class="form__error">
        @error('categories')
        {{ $message }}
        @enderror
    </div>

    <!-- 商品の状態 -->
    <div class="content__title">商品の状態</div>
    <div class="condition_selection">
        <select name="condition" class="form-select">
            <option value="" disabled {{ old('condition') ? '' : 'selected' }}>選択してください</option>
            <option {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
            <option {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
            <option {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
            <option {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
        </select>
    </div>
    <div class="form__error">
        @error('condition')
        {{ $message }}
        @enderror
    </div>

    <!-- 商品名と説明 -->
    <h2>商品と説明</h2>

    <!-- 商品名 -->
    <div class="content__title">商品名</div>
    <div class="form__input--text">
        <input type="text" name="name" value="{{ old('name') }}" />
    </div>
    <div class="form__error">
        @error('name')
        {{ $message }}
        @enderror
    </div>

    <!-- ブランド名 -->
    <div class="content__title">ブランド名</div>
    <div class="form__input--text">
        <input type="text" name="brand" value="{{ old('brand') }}" />
    </div>
    <div class="form__error">
        @error('brand')
        {{ $message }}
        @enderror
    </div>

    <!-- 商品の説明 -->
    <div class="content__title">商品の説明</div>
    <div class="form__input--text">
        <input type="textarea" name="description" value="{{ old('description') }}" />
    </div>
    <div class="form__error">
        @error('description')
        {{ $message }}
        @enderror
    </div>

    <!-- 販売価格 -->
    <div class="content__title">販売価格</div>
    <div class="form__input--text">
        <input type="number" name="price" value="{{ old('price') }}" />
    </div>
    <div class="form__error">
        @error('price')
        {{ $message }}
        @enderror
    </div>

    <!-- 出品する -->
    <button class="form__button-submit submit__button" type="submit">出品する</button>
    @endsection