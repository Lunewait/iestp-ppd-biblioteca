<?php

use App\Http\Controllers\FineController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'IESTP Library Platform API',
        'version' => '1.0.0',
        'timestamp' => now(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    // User endpoints
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });

    // Materials endpoints
    Route::prefix('materials')->group(function () {
        Route::get('/', [MaterialController::class, 'index']);
        Route::get('/{material}', [MaterialController::class, 'show']);
        Route::post('/', [MaterialController::class, 'store'])->middleware('permission:create_material');
        Route::put('/{material}', [MaterialController::class, 'update'])->middleware('permission:edit_material');
        Route::delete('/{material}', [MaterialController::class, 'destroy'])->middleware('permission:delete_material');
    });

    // Loans endpoints
    Route::prefix('loans')->group(function () {
        Route::get('/', [LoanController::class, 'index']);
        Route::get('/{loan}', [LoanController::class, 'show']);
        Route::post('/', [LoanController::class, 'store'])->middleware('permission:create_loan');
        Route::post('/{loan}/return', [LoanController::class, 'return'])->middleware('permission:return_loan');
    });

    // Fines endpoints
    Route::prefix('fines')->group(function () {
        Route::get('/', [FineController::class, 'index'])->middleware('permission:view_fines');
        Route::get('/{fine}', [FineController::class, 'show'])->middleware('permission:view_fines');
        Route::post('/', [FineController::class, 'store'])->middleware('permission:create_fine');
        Route::put('/{fine}', [FineController::class, 'update'])->middleware('permission:manage_fines');
        Route::post('/{fine}/mark-as-paid', [FineController::class, 'markAsPaid'])->middleware('permission:manage_fines');
        Route::post('/{fine}/forgive', [FineController::class, 'forgive'])->middleware('permission:forgive_fine');
        Route::delete('/{fine}', [FineController::class, 'destroy'])->middleware('permission:manage_fines');
    });

    // Reservations endpoints
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->middleware('permission:view_reservations');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->middleware('permission:view_reservations');
        Route::post('/', [ReservationController::class, 'store'])->middleware('permission:create_reservation');
        Route::put('/{reservation}', [ReservationController::class, 'update'])->middleware('permission:manage_reservations');
        Route::post('/{reservation}/complete', [ReservationController::class, 'complete'])->middleware('permission:manage_reservations');
        Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->middleware('permission:manage_reservations');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->middleware('permission:manage_reservations');
    });

    // Repository endpoints
    Route::prefix('repository')->group(function () {
        Route::get('/', [RepositoryController::class, 'index']);
        Route::get('/{documento}', [RepositoryController::class, 'show']);
        Route::get('/{documento}/download', [RepositoryController::class, 'download']);
        Route::post('/', [RepositoryController::class, 'store'])->middleware('permission:submit_document');
        Route::post('/{documento}/approve', [RepositoryController::class, 'processApproval'])->middleware('permission:approve_document');
    });

    // Users endpoints (Admin only)
    Route::prefix('users')->middleware('role:Admin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:view_users');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:view_users');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create_user');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:edit_user');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete_user');
        Route::post('/{user}/change-role', [UserController::class, 'changeRole'])->middleware('permission:manage_roles');
    });
});
