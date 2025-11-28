<?php

use App\Http\Controllers\FineController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard - Blocked for Jefe_Area and Estudiante
    Route::get('/dashboard', function () {
        // Redirect Jefe_Area to repository
        if (auth()->user()->hasRole('Jefe_Area')) {
            return redirect()->route('repository.index');
        }
        // Redirect Estudiante to materials catalog
        if (auth()->user()->hasRole('Estudiante')) {
            return redirect()->route('materials.index');
        }
        return view('dashboard');
    })->name('dashboard');

    // Materials Catalog
    Route::get('materials', [MaterialController::class, 'index'])->name('materials.index')->middleware('permission:view_materials');

    // Rutas específicas ANTES de rutas con parámetros
    Route::get('materials/create', [MaterialController::class, 'create'])->name('materials.create')->middleware('permission:create_material');
    Route::post('materials', [MaterialController::class, 'store'])->name('materials.store')->middleware('permission:create_material');

    // Rutas con parámetros {material} al FINAL
    Route::get('materials/{material}', [MaterialController::class, 'show'])->name('materials.show')->middleware('permission:view_materials');
    Route::get('materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit')->middleware('permission:edit_material');
    Route::patch('materials/{material}', [MaterialController::class, 'update'])->name('materials.update')->middleware('permission:edit_material');
    Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy')->middleware('permission:delete_material');

    // Loans Management
    Route::get('loans', [LoanController::class, 'index'])->name('loans.index')->middleware('permission:view_loans');

    // Rutas específicas ANTES de rutas con parámetros
    Route::get('loans/create', [LoanController::class, 'create'])->name('loans.create')->middleware('permission:create_loan');
    Route::post('loans', [LoanController::class, 'store'])->name('loans.store')->middleware('permission:create_loan');

    // Rutas con parámetros {loan} al FINAL
    Route::get('loans/{loan}', [LoanController::class, 'show'])->name('loans.show')->middleware('permission:view_loans');
    Route::get('loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return-form')->middleware('permission:return_loan');
    Route::post('loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return')->middleware('permission:return_loan');

    // Loan Requests & Approvals
    Route::get('loan-requests', function () {
        return view('loan-requests');
    })->name('loan-requests.index')->middleware('role:Estudiante');

    Route::get('loan-approvals', function () {
        return view('loan-approvals');
    })->name('loan-approvals.index')->middleware('permission:approve_loan');

    // Fines Management
    Route::get('fines', [FineController::class, 'index'])->name('fines.index')->middleware('permission:view_fines');

    // Rutas específicas ANTES de rutas con parámetros
    Route::get('fines/create', [FineController::class, 'create'])->name('fines.create')->middleware('permission:create_fine');
    Route::post('fines', [FineController::class, 'store'])->name('fines.store')->middleware('permission:create_fine');

    // Rutas con parámetros {fine} al FINAL
    Route::get('fines/{fine}', [FineController::class, 'show'])->name('fines.show')->middleware('permission:view_fines');
    Route::get('fines/{fine}/edit', [FineController::class, 'edit'])->name('fines.edit')->middleware('permission:manage_fines');
    Route::patch('fines/{fine}', [FineController::class, 'update'])->name('fines.update')->middleware('permission:manage_fines');
    Route::post('fines/{fine}/mark-as-paid', [FineController::class, 'markAsPaid'])->name('fines.mark-as-paid')->middleware('permission:manage_fines');
    Route::post('fines/{fine}/forgive', [FineController::class, 'forgive'])->name('fines.forgive')->middleware('permission:forgive_fine');
    Route::delete('fines/{fine}', [FineController::class, 'destroy'])->name('fines.destroy')->middleware('permission:manage_fines');

    // Reservations Management
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index')->middleware('permission:view_reservations');
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show')->middleware('permission:view_reservations');

    Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create')->middleware('permission:create_reservation');
    Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store')->middleware('permission:create_reservation');
    Route::get('reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit')->middleware('permission:manage_reservations');
    Route::patch('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update')->middleware('permission:manage_reservations');
    Route::post('reservations/{reservation}/approve', [ReservationController::class, 'approve'])->name('reservations.approve')->middleware('permission:manage_reservations');
    Route::post('reservations/{reservation}/complete', [ReservationController::class, 'complete'])->name('reservations.complete')->middleware('permission:manage_reservations');
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel')->middleware('permission:manage_reservations');
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy')->middleware('permission:manage_reservations');

    // User Management
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view_users');

    // User Import (debe estar ANTES de users/create)
    Route::get('users/import/form', [App\Http\Controllers\UserImportController::class, 'showImportForm'])->name('users.import.form')->middleware('permission:create_user');
    Route::post('users/import/process', [App\Http\Controllers\UserImportController::class, 'import'])->name('users.import.process')->middleware('permission:create_user');
    Route::get('users/import/template', [App\Http\Controllers\UserImportController::class, 'downloadTemplate'])->name('users.import.template')->middleware('permission:create_user');

    // User CRUD (rutas específicas ANTES de rutas con parámetros)
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create_user');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create_user');

    // Rutas con parámetros {user} al FINAL
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:view_users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit_user');
    Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit_user');
    Route::post('users/{user}/change-role', [UserController::class, 'changeRole'])->name('users.change-role')->middleware('permission:manage_roles');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete_user');

    // Repository
    Route::get('repository', [RepositoryController::class, 'index'])->name('repository.index')->middleware('permission:view_repository');

    // Rutas específicas ANTES de rutas con parámetros
    Route::get('repository/create', [RepositoryController::class, 'create'])->name('repository.create')->middleware('permission:submit_document');
    Route::post('repository', [RepositoryController::class, 'store'])->name('repository.store')->middleware('permission:submit_document');

    // Rutas con parámetros {documento} al FINAL
    Route::get('repository/{documento}', [RepositoryController::class, 'show'])->name('repository.show')->middleware('permission:view_repository');
    Route::get('repository/{documento}/download', [RepositoryController::class, 'download'])->name('repository.download');
    Route::get('repository/{documento}/approve', [RepositoryController::class, 'approve'])->name('repository.approve')->middleware('permission:approve_document');
    Route::post('repository/{documento}/approve', [RepositoryController::class, 'processApproval'])->name('repository.process-approval')->middleware('permission:approve_document');
    Route::delete('repository/{documento}', [RepositoryController::class, 'destroy'])->name('repository.destroy')->middleware('role:Admin');
});

require __DIR__ . '/auth.php';
