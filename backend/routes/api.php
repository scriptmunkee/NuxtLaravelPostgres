<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Categories and breeds (public)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/categories/{category}/breeds', [CategoryController::class, 'breeds']);

// Locations (public)
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/locations/search', [LocationController::class, 'search']);
Route::get('/locations/{location}', [LocationController::class, 'show']);

// Listings (public browsing)
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/{listing}', [ListingController::class, 'show']);
Route::get('/listings/search', [ListingController::class, 'search']);

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

    // Listings management
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{listing}', [ListingController::class, 'update']);
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy']);
    Route::get('/my-listings', [ListingController::class, 'myListings']);

    // Image upload
    Route::post('/listings/{listing}/images', [ListingController::class, 'uploadImages']);
    Route::delete('/listings/images/{image}', [ListingController::class, 'deleteImage']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/listings/{listing}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/listings/{listing}/unfavorite', [FavoriteController::class, 'destroy']);

    // Messages
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::put('/messages/{message}/read', [MessageController::class, 'markAsRead']);
});
