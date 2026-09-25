<?php

use App\Http\Controllers\API\APIController;
use App\Http\Controllers\API\CategoryAPI;
use App\Http\Controllers\API\ItemAPI;
use App\Http\Controllers\API\LanguageAPI;
use App\Http\Controllers\API\MediaAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/items/index', [ItemAPI::class, 'index']);
Route::get('/items/all', [ItemAPI::class, 'all']);
Route::get('/items/{id_type}/{id}', [ItemAPI::class, 'get'])->whereIn('id_type', ['id', 'slug']);

Route::get('/categories/index', [CategoryAPI::class, 'index']);
Route::get('/categories/all', [CategoryAPI::class, 'all']);
Route::get('/categories/{id_type}/{id}', [CategoryAPI::class, 'get'])->whereIn('id_type', ['id', 'slug']);

Route::get('/languages/all', [LanguageAPI::class, 'all']);
Route::get('/languages/{id_type}/{id}', [LanguageAPI::class, 'get'])->whereIn('id_type', ['id', 'name']);

Route::get('/media/index', [MediaAPI::class, 'index']);
Route::get('/media/all', [MediaAPI::class, 'all']);
Route::get('/media/id/{id}', [MediaAPI::class, 'get']);