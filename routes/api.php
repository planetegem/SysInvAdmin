<?php

use App\Http\Controllers\API\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/items/index', [APIController::class, 'indexItems']);
Route::get('/items/all', [APIController::class, 'allItems']);
Route::get('/items/id/{id}', [APIController::class, 'getItemById']);
Route::get('/items/slug/{slug}', [APIController::class, 'getItemBySlug']);


Route::get('/categories/index', [APIController::class, 'indexCategories']);
Route::get('/categories/all', [APIController::class, 'allCategories']);
Route::get('/categories/id/{id}', [APIController::class, 'getCategoryById']);
Route::get('/categories/slug/{id}', [APIController::class, 'getCategoryBySlug']);

Route::get('/languages/all', [APIController::class, 'allLanguages']);
Route::get('/languages/id/{id}', [APIController::class, 'getLanguageById']);
Route::get('/languages/name/{short}', [APIController::class, 'getLanguageByShortName']);



