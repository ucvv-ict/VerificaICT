<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->prefix('operatore')->group(function () {

    Route::get('/', [\App\Http\Controllers\OperatorDashboardController::class, 'index'])
        ->name('operator.dashboard');

    Route::get('/check/{entitySecurityTask}', [\App\Http\Controllers\OperatorCheckController::class, 'show'])
        ->name('operator.check.show');

    Route::post('/check/{entitySecurityTask}', [\App\Http\Controllers\OperatorCheckController::class, 'store'])
        ->name('operator.check.store');
});
