<?php

use App\Http\Controllers\API\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/items/index', [APIController::class, 'indexItems']);
Route::get('/items/all', [APIController::class, 'allItems']);
Route::get('/items/{id}', [APIController::class, 'getItem']);

Route::get('/categories/index', [APIController::class, 'indexCategories']);
Route::get('/categories/all', [APIController::class, 'allCategories']);
Route::get('/categories/{id}', [APIController::class, 'getCategory']);

Route::get('/languages', [APIController::class, 'getLanguages']);
Route::get('/languages/{id}', [APIController::class, 'getLanguage']);



