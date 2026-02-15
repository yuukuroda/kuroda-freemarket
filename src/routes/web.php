<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GoodController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;

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

Route::middleware('auth')->group(
    function () {
        Route::post('/item/{itemId}/good', [GoodController::class, 'add'])->name('add');
        Route::delete('/item/{itemId}/destroy', [GoodController::class, 'destroy'])->name('destroy');
        Route::post('/item/{itemId}/comment', [CommentController::class, 'store'])->name('store');
        Route::get('/purchase/{itemId}', [PurchaseController::class, 'create'])->name('purchase.create');
        Route::get('/mypage/profile', [ProfileController::class, 'create'])->name('profile.create');
        Route::post('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    }
);
