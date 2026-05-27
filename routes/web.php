<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backoffice\DashboardController;
use App\Http\Controllers\Backoffice\CategoryController;
use App\Http\Controllers\Backoffice\UserController;
use App\Http\Controllers\Backoffice\PaymentPlanController;
use App\Http\Controllers\Backoffice\DepositController;
use App\Http\Controllers\Backoffice\TransactionController;
use App\Http\Controllers\Backoffice\ReportController;
use App\Http\Controllers\Backoffice\GoogleDriveProxyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Backoffice Dashboard named 'dashboard' for Breeze compatibility
Route::get('/backoffice', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

// Backoffice Resource routes prefixed with name 'backoffice.'
Route::middleware(['auth', 'admin'])->prefix('backoffice')->name('backoffice.')->group(function () {
    // Category Management
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // User Management
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Tagihan / Payment Plans (readonly + view detail)
    Route::get('payment-plans', [PaymentPlanController::class, 'index'])->name('payment-plans.index');
    Route::get('payment-plans/{paymentPlan}', [PaymentPlanController::class, 'show'])->name('payment-plans.show');

    // Deposits (readonly)
    Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index');

    // Transactions (readonly + view detail)
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('reports/xlsx', [ReportController::class, 'downloadXlsx'])->name('reports.xlsx');

    // System Logs
    Route::get('logs', [\App\Http\Controllers\Backoffice\LogController::class, 'index'])->name('logs.index');

    // Google Drive Proxy Preview
    Route::get('gdrive/preview', [GoogleDriveProxyController::class, 'preview'])->name('gdrive.preview');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

