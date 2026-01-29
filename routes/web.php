<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\ItemController;


use App\Http\Middleware\UserIsAdmin;



Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');

    Route::get('/register/{uuid}', [RegisterController::class, 'create'])->name('register.create');
    Route::post('/register/{uuid}/store', [RegisterController::class, 'store'])->name('register.store');


});



Route::middleware('auth')->group(function () {
    Route::view('/', 'home')->name('home');

    Route::middleware(UserIsAdmin::class)->group(function () {
        Route::view('/settings', 'under_construction');
    });

    Route::resource('media', MediumController::class);

    Route::view('/settings', 'under_construction');

    Route::resource('items', controller: ItemController::class);
    Route::resource('categories', CategoryController::class);

});

Route::post('/logout', [LoginController::class, 'destroy'])->name('login.destroy');
