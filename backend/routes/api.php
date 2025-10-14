<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetListingController;
use App\Http\Controllers\Api\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/pet-listings', [PetListingController::class, 'index']);
Route::get('/pet-listings/{petListing}', [PetListingController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::apiResource('pet-listings', PetListingController::class)->except(['index', 'show']);
    Route::get('/my-listings', [PetListingController::class, 'myListings']);
    
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{petListing}/toggle', [FavoriteController::class, 'toggle']);
});
