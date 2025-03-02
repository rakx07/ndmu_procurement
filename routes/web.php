<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ITAdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AuditTrailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Laravel Breeze Authentication Routes
|--------------------------------------------------------------------------
| These routes are handled by Laravel Breeze, so we keep them intact.
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Dashboard (Authenticated Users Only)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Management (Default Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Middleware Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Force Password Change on First Login
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])
        ->name('change_password_form');
    Route::post('/change-password', [ChangePasswordController::class, 'updatePassword'])
        ->name('update_password');
        Route::get('/settings', function () {
            return view('settings'); // Ensure settings.blade.php exists in resources/views
        })->name('settings');
        Route::get('/settings', function () {
            return view('settings');
        })->name('settings');
        

    /*
    |--------------------------------------------------------------------------
    | IT Admin Routes (Role: 5)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:5'])->group(function () {
        // IT Admin Dashboard
        Route::get('/dashboard', [ITAdminController::class, 'dashboard'])->name('dashboard'); // Separate from user-management
    
        // User Management
        Route::get('/user-management', [ITAdminController::class, 'index'])->name('user.management');
    
        // User Creation
        Route::get('/it-admin/create', [ITAdminController::class, 'create'])->name('it_admin.create');
        Route::post('/it-admin/store', [ITAdminController::class, 'store'])->name('it_admin.store');
    
        // Edit & Update User
        Route::put('/users/{id}/update', [ITAdminController::class, 'update'])->name('users.update');
    
        // Toggle Active/Inactive Status
        Route::post('/it_admin/toggle-status/{id}', [ITAdminController::class, 'toggleStatus'])->name('it_admin.toggleStatus');
    
        // Suspend User
        Route::post('/it_admin/suspend/{id}', [ITAdminController::class, 'suspend'])->name('it_admin.suspend');
    });
    

    /*
    |--------------------------------------------------------------------------
    | Procurement Request Routes (For Staff)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:0'])->group(function () {
        Route::resource('procurement-requests', ProcurementRequestController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Approval Routes (For Supervisors, Admin, Comptroller)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:2', 'role:3', 'role:4'])->group(function () {
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Purchase Routes (For Purchasing Officer)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:1'])->group(function () {
        Route::resource('purchases', PurchaseController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Audit Trails (For Admins)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:3'])->group(function () {
        Route::get('audit-trails', [AuditTrailController::class, 'index'])->name('audit_trails.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings Route
    |--------------------------------------------------------------------------
    */
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
});
