<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminBulkAssignmentController;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect('/admin/login');
    }

    return auth()->user()->isAdmin()
        ? redirect('/admin')
        : redirect('/operatore');
});


Route::middleware(['auth', 'operator'])
    ->prefix('operatore')
    ->group(function () {

        Route::get('/', [\App\Http\Controllers\OperatorDashboardController::class, 'index'])
            ->name('operator.dashboard');

        Route::get('/check/{entitySecurityTask}', [\App\Http\Controllers\OperatorCheckController::class, 'show'])
            ->name('operator.check.show');

        Route::post('/check/{entitySecurityTask}', [\App\Http\Controllers\OperatorCheckController::class, 'store'])
            ->name('operator.check.store');

        Route::post(
            '/task/{entitySecurityTask}/quick-check',
            [\App\Http\Controllers\OperatorQuickCheckController::class, 'store']
        )->name('operator.quick-check');
    });


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/assegnazioni-massive', [AdminBulkAssignmentController::class, 'index'])
            ->name('admin.bulk.index');

        Route::post('/assegnazioni-massive', [AdminBulkAssignmentController::class, 'store'])
            ->name('admin.bulk.store');

        Route::post('/assegnazioni-massive/preview',
            [AdminBulkAssignmentController::class, 'preview']
        )->name('admin.bulk.preview');

        Route::get('/audit-log',
            [\App\Http\Controllers\AdminAuditLogController::class, 'index']
        )->name('admin.audit.index');
    });
