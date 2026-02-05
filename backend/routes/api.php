<?php

use App\Http\Controllers\FoiController;
use Illuminate\Support\Facades\Route;

// FOI Request Management
Route::prefix('foi')->group(function () {
    Route::get('/dashboard', [FoiController::class, 'dashboard']);
    Route::get('/requests', [FoiController::class, 'index']);
    Route::post('/requests/import', [FoiController::class, 'import']);
    Route::get('/requests/{foiRequest}', [FoiController::class, 'show']);
    Route::put('/requests/{foiRequest}', [FoiController::class, 'update']);
    Route::delete('/requests/{foiRequest}', [FoiController::class, 'destroy']);
    Route::post('/requests/{foiRequest}/poll', [FoiController::class, 'poll']);

    Route::get('/status-updates', [FoiController::class, 'statusUpdates']);
    Route::post('/status-updates/{foiStatusUpdate}/acknowledge', [FoiController::class, 'acknowledgeUpdate']);
    Route::post('/status-updates/acknowledge-all', [FoiController::class, 'acknowledgeAll']);
});
