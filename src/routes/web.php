<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GoodController;
use App\Http\Controllers\CommentController;

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

Route::middleware('auth')->group(
    function () {
        Route::get('/', [ItemController::class, 'index']);
    }
);
Route::get('/item/{itemId}', [ItemController::class, 'show'])->name('item.show');
Route::post('/items/{item}', [GoodController::class, 'add'])->name('add');
Route::delete('/items/{item}', [GoodController::class, 'destroy'])->name('destroy');
Route::post('/item/{itemId}', [CommentController::class, 'store'])->name('store');
