<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashPeriodController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseBatchController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Anggota
    Route::resource('members', MemberController::class)->except(['show']);

    // Periode kas
    Route::resource('periods', CashPeriodController::class)->except(['show']);
    Route::post('periods/{period}/activate', [CashPeriodController::class, 'activate'])->name('periods.activate');

    // Sub-resource per periode
    Route::prefix('periods/{period}')->group(function () {
        // Iuran / pembayaran
        Route::get('payments', [PaymentController::class, 'index'])->name('periods.payments.index');
        Route::patch('payments/{payment}', [PaymentController::class, 'update'])->name('periods.payments.update');
        Route::post('payments/{member}/paid', [PaymentController::class, 'markPaid'])->name('periods.payments.markPaid');
        Route::post('payments/{member}/unpaid', [PaymentController::class, 'markUnpaid'])->name('periods.payments.markUnpaid');
        Route::post('payments/mark-all-paid', [PaymentController::class, 'markAllPaid'])->name('periods.payments.markAllPaid');

        // Pengeluaran
        Route::get('expenses/import', [ExpenseController::class, 'importForm'])->name('periods.expenses.importForm');
        Route::post('expenses/import', [ExpenseController::class, 'import'])->name('periods.expenses.import');
        Route::get('expenses/template', [ExpenseController::class, 'downloadTemplate'])->name('periods.expenses.template');
        Route::resource('expenses', ExpenseController::class)
            ->except(['show'])
            ->names('periods.expenses');

        // Batch pengeluaran
        Route::resource('batches', ExpenseBatchController::class)
            ->except(['show'])
            ->names('periods.batches');

        // Laporan
        Route::get('report', [ReportController::class, 'show'])->name('periods.report.show');
        Route::get('report/csv', [ReportController::class, 'exportCsv'])->name('periods.report.csv');
    });
});
