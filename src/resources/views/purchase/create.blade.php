@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}">
@endsection

@section('content')
<div class="content">
    <form>
        <div class="left">
            <div class="top">
                <!-- 商品画像 -->
                <div class="item_img">
                    <img class="img_preview" src="{{'/storage/'.$item->image}}">
                </div>
                <!-- 商品名 -->
                <div class="item_name">{{ $item->name }}</div>
                <!-- 価格 -->
                <div class="item_price">{{ $item->price }}</div>
            </div>

            <div class="middle">
                <!-- 支払方法 -->
                <div class="content_title">
                    支払い方法
                </div>
                <div class="payment_selection">
                    <select name="payment_method" id="payment_method_select" class="form-select">
                        <option value="" disabled selected>選択してください</option>
                        <option value="konbini">コンビニ支払い</option>
                        <option value="card">カード支払い</option>
                    </select>
                </div>
            </div>
            <div class="bottom">
                <!-- 配送先 -->
                <div class="content_title">
                    配送先
                </div>
            </div>


            <div class="right">
                <!-- 商品代金 -->
                <div class="payment_title">商品代金</div>
                <div class="item_name">{{ $item->price }}</div>
                <!-- 支払い方法 -->
                <div class="payment_title">支払い方法</div>

            </div>
        </div>
    </form>
</div>
@endsection