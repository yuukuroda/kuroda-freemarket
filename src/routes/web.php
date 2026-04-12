<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GoodController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index'])->name('item.index');
Route::get('/item/{itemId}', [ItemController::class, 'show'])->name('item.show');
Route::get('/search', [ItemController::class, 'search']);

Route::middleware('auth')->group(
    function () {
        // 1. メール確認の通知画面を表示するルート
        Route::get('/email/verify', function () {
            return view('auth.verify-email');
        })->middleware('auth')->name('verification.notice');

        // 2. メール内のリンクをクリックした時の処理
        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();
            return redirect('/profile.show');
        })->middleware(['auth', 'signed'])->name('verification.verify');

        // 3. 確認メールの再送処理
        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('message', '確認メールを再送信しました。');
        })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

        Route::middleware('verified')->group(
            function () {
        
        Route::post('/item/{itemId}/good', [GoodController::class, 'add'])->name('add');
        Route::delete('/item/{itemId}/destroy', [GoodController::class, 'destroy'])->name('destroy');

        Route::post('/item/{itemId}/comment', [CommentController::class, 'store'])->name('store');

        Route::get('/purchase/{itemId}', [PurchaseController::class, 'create'])->name('purchase.create');
        Route::get('/purchase/address/{itemId}', [PurchaseController::class, 'address'])->name('purchase.address');
        Route::post('/purchase/address/{itemId}/update', [PurchaseController::class, 'update'])->name('purchase.address.update');

        Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.show');
        Route::post('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');

        // 購入ボタン押下時のエントリーポイント
        Route::post('/purchase/store/{itemId}', [PurchaseController::class, 'store'])->name('purchase.store');

        // カード決済：成功時（ここでテーブル保存と一覧リダイレクトを行う）
        Route::get('/purchase/success/{itemId}', [PurchaseController::class, 'success'])->name('purchase.success');

        // コンビニ払い：完了時
        Route::get('/purchase/konbini-complete/{itemId}', [PurchaseController::class, 'konbiniComplete'])->name('purchase.konbini_complete');

        // キャンセル時
        Route::get('/purchase/show/{itemId}', [PurchaseController::class, 'show'])->name('purchase.show');

        Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
        Route::post('/sell/store', [ItemController::class, 'store'])->name('sell.store');
    });
    }

);
