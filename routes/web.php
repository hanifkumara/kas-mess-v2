<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashPeriodController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseBatchController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PasskeyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WebAuthnController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest: login + passkey login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    // Passkey (WebAuthn) assertion ceremony
    Route::post('/passkey/login/options', [WebAuthnController::class, 'loginOptions'])->name('passkey.login.options');
    Route::post('/passkey/login', [WebAuthnController::class, 'loginVerify'])->name('passkey.login');
});

/*
|--------------------------------------------------------------------------
| Authenticated area
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Passkey milik sendiri (attestation + manajemen) — semua user
    Route::post('/passkey/options', [WebAuthnController::class, 'registerOptions'])->name('passkey.options');
    Route::post('/passkey', [WebAuthnController::class, 'registerStore'])->name('passkey.store');
    Route::get('/passkeys', [PasskeyController::class, 'index'])->name('passkeys.index');
    Route::delete('/passkeys/{id}', [PasskeyController::class, 'destroy'])->name('passkeys.destroy');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');

    // Administrasi pengguna & role (RBAC)
    Route::middleware('permission:users.manage')->group(function () {
        Route::resource('admins', AdminController::class)->except(['show']);
    });
    Route::middleware('permission:roles.manage')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
    });

    // Anggota
    Route::resource('members', MemberController::class)
        ->except(['show'])
        ->middleware('permission:members.manage');

    // Periode kas
    Route::resource('periods', CashPeriodController::class)
        ->except(['show'])
        ->middleware('permission:periods.manage');
    Route::post('periods/{period}/activate', [CashPeriodController::class, 'activate'])
        ->middleware('permission:periods.manage')->name('periods.activate');

    // Sub-resource per periode
    Route::prefix('periods/{period}')->group(function () {
        // Iuran: lihat (view) vs ubah (manage)
        Route::get('payments', [PaymentController::class, 'index'])->middleware('permission:payments.view')->name('periods.payments.index');
        Route::middleware('permission:payments.manage')->group(function () {
            Route::patch('payments/{payment}', [PaymentController::class, 'update'])->name('periods.payments.update');
            Route::post('payments/{member}/paid', [PaymentController::class, 'markPaid'])->name('periods.payments.markPaid');
            Route::post('payments/{member}/unpaid', [PaymentController::class, 'markUnpaid'])->name('periods.payments.markUnpaid');
            Route::post('payments/mark-all-paid', [PaymentController::class, 'markAllPaid'])->name('periods.payments.markAllPaid');
        });

        // Pengeluaran: lihat (view) vs ubah (manage)
        Route::middleware('permission:expenses.view')->group(function () {
            Route::get('expenses', [ExpenseController::class, 'index'])->name('periods.expenses.index');
            Route::get('expenses/create', [ExpenseController::class, 'create'])->name('periods.expenses.create');
            Route::get('expenses/import', [ExpenseController::class, 'importForm'])->name('periods.expenses.importForm');
            Route::get('expenses/template', [ExpenseController::class, 'downloadTemplate'])->name('periods.expenses.template');
        });
        Route::middleware('permission:expenses.manage')->group(function () {
            Route::post('expenses', [ExpenseController::class, 'store'])->name('periods.expenses.store');
            Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('periods.expenses.edit');
            Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('periods.expenses.update');
            Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('periods.expenses.destroy');
            Route::post('expenses/import', [ExpenseController::class, 'import'])->name('periods.expenses.import');
        });

        // Batch pengeluaran (ikut permission expenses.manage)
        Route::resource('batches', ExpenseBatchController::class)
            ->except(['show'])
            ->middleware('permission:expenses.manage')
            ->names('periods.batches');

        // Laporan
        Route::get('report', [ReportController::class, 'show'])->middleware('permission:reports.view')->name('periods.report.show');
        Route::get('report/csv', [ReportController::class, 'exportCsv'])->middleware('permission:reports.view')->name('periods.report.csv');
        Route::get('report/excel', [ReportController::class, 'exportExcel'])->middleware('permission:reports.view')->name('periods.report.excel');
        Route::get('report/pdf', [ReportController::class, 'exportPdf'])->middleware('permission:reports.view')->name('periods.report.pdf');
    });
});
