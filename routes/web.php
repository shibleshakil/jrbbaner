<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/profile/{name}', [AccountController::class, 'viewProfile'])->name('profile');
    Route::post('/profile/{name}', [AccountController::class, 'updateProfile']);

    Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
        Route::get('update', [AccountController::class, 'showPasswordForm'])->name('update');
        Route::post('update', [AccountController::class, 'updatePassword']);
    });

    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/preview', [PromotionController::class, 'preview'])->name('promotions.preview');
    Route::get('/promotions/{promotion}/download', [PromotionController::class, 'download'])->name('promotions.download');

    Route::get('/promotions/{promotion}/regenerate', [PromotionController::class, 'regenerate'])->name('promotions.regenerate');
    Route::get('/promotions/{promotion}/export-png', [PromotionController::class, 'exportPng'])->name('promotions.exportPng');
});
