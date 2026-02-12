<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminBulkAssignmentController;

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


Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/assegnazioni-massive', [AdminBulkAssignmentController::class, 'index'])
        ->name('admin.bulk.index');

    Route::post('/assegnazioni-massive', [AdminBulkAssignmentController::class, 'store'])
        ->name('admin.bulk.store');

    Route::post('/assegnazioni-massive/preview',
    [AdminBulkAssignmentController::class, 'preview']
        )->name('admin.bulk.preview');

    Route::get('/dashboard-globale',
        [\App\Http\Controllers\AdminGlobalDashboardController::class, 'index']
    )->name('admin.dashboard.global');

    Route::get('/audit-log',
        [\App\Http\Controllers\AdminAuditLogController::class, 'index']
    )->name('admin.audit.index');

});
