<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GoodController;

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
Route::get('/item/{itemId}', [ItemController::class, 'show']);
Route::post('/items/{item}', [GoodController::class, 'store'])->name('store');
Route::delete('/items/{item}', [GoodController::class, 'destroy'])->name('destroy');