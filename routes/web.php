<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AvailabilityBannerController;
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
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/preview', [PromotionController::class, 'preview'])->name('promotions.preview');
    Route::get('/promotions/{promotion}/download', [PromotionController::class, 'download'])->name('promotions.download');

    Route::get('/promotions/{promotion}/regenerate', [PromotionController::class, 'regenerate'])->name('promotions.regenerate');
    Route::get('/promotions/{promotion}/export-png', [PromotionController::class, 'exportPng'])->name('promotions.exportPng');

    Route::get('/availability-banners', [AvailabilityBannerController::class, 'index'])->name('availability_banners.index');
    Route::get('/availability-banners/create', [AvailabilityBannerController::class, 'create'])->name('availability_banners.create');
    Route::post('/availability-banners', [AvailabilityBannerController::class, 'store'])->name('availability_banners.store');
    Route::get('/availability-banners/{availability_banner}/preview', [AvailabilityBannerController::class, 'preview'])->name('availability_banners.preview');
    Route::get('/availability-banners/{availability_banner}/download', [AvailabilityBannerController::class, 'download'])->name('availability_banners.download');
    Route::get('/availability-banners/{availability_banner}', [AvailabilityBannerController::class, 'show'])->name('availability_banners.show');
});
