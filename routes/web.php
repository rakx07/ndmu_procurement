<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ITAdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AuditTrailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes (Accessible without Authentication)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Laravel Breeze Authentication Routes (Login, Register, Logout)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Dashboard - Redirect Users Based on Their Role
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->check()) {
        switch (auth()->user()->role) {
            case 0:
                return redirect()->route('staff.dashboard'); // Staff
            case 2:
                return redirect()->route('supervisor.dashboard'); // Supervisor
            case 5:
                return redirect()->route('it_admin.dashboard'); // IT Admin
            default:
                return view('dashboard'); // Placeholder for other roles
        }
    }
    return redirect('/login'); // Unauthenticated users
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Management (Available for All Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Password Change Routes (Force Password Change on First Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('change_password_form');
    Route::post('/change-password', [ChangePasswordController::class, 'updatePassword'])->name('update_password');
});

/*
|--------------------------------------------------------------------------
| Staff Routes (Role: 0) - Staff Users Only
|--------------------------------------------------------------------------
*/
// Staff Routes (Only Role 0 can access)
Route::middleware(['auth', 'role:0'])->prefix('staff')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');

    // Procurement Request Management
    Route::get('/request/create', [ProcurementRequestController::class, 'create'])->name('staff.requests.create');
    Route::post('/request', [StaffController::class, 'store'])->name('staff.requests.store');
    Route::get('/request/{id}/edit', [StaffController::class, 'edit'])->name('staff.requests.edit');
    Route::put('/request/{id}', [StaffController::class, 'update'])->name('staff.requests.update');

    // Procurement Requests - Viewing and Storage
    Route::get('/requests', [ProcurementRequestController::class, 'index'])->name('staff.requests.index');
    Route::post('/requests/store', [ProcurementRequestController::class, 'store'])->name('staff.requests.store');
    Route::get('/requests/{id}', [ProcurementRequestController::class, 'show'])->name('staff.requests.show');

    // ✅ FIX: Removed extra "staff/" to avoid duplication
    Route::get('/request/{id}/items', [ProcurementRequestController::class, 'getRequestItems'])->name('staff.requests.items');
});

// ✅ FIX: Allow all authenticated users to add items
Route::middleware(['auth'])->group(function () {
    Route::post('/staff/request/add-item', [ProcurementRequestController::class, 'addItem'])->name('staff.requests.addItem');
});


/*
|--------------------------------------------------------------------------
| Supervisor Routes (Role: 2) - Supervisors Only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:2'])->group(function () {
    Route::get('/supervisor/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
    Route::get('/supervisor/request/{id}', [SupervisorController::class, 'show'])->name('supervisor.show');
    Route::get('/supervisor/approve/{id}', [SupervisorController::class, 'approveRequestView'])->name('supervisor.approve_request');
    Route::post('/supervisor/approve/{id}', [SupervisorController::class, 'approve'])->name('supervisor.approve');
    Route::post('/supervisor/reject/{id}', [SupervisorController::class, 'reject'])->name('supervisor.reject');
    Route::get('/supervisor/request/{id}/items', [SupervisorController::class, 'getRequestItems']);
    Route::get('/supervisor/approved-requests', [SupervisorController::class, 'approvedRequests'])
    ->name('supervisor.approved_requests');
    Route::get('/supervisor/approved-request/{id}/items', [SupervisorController::class, 'getApprovedRequestItems']);


});

/*
|--------------------------------------------------------------------------
| IT Admin Routes (Role: 5) - IT Admin Users Only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:5'])->group(function () {
    Route::get('/it-admin/dashboard', [ITAdminController::class, 'dashboard'])->name('it_admin.dashboard');
    Route::get('/user-management', [ITAdminController::class, 'index'])->name('user.management');
    Route::get('/it-admin/create', [ITAdminController::class, 'create'])->name('it_admin.create');
    Route::post('/it-admin/store', [ITAdminController::class, 'store'])->name('it_admin.store');
    Route::post('/it_admin/toggle-status/{id}', [ITAdminController::class, 'toggleStatus'])->name('it_admin.toggleStatus');
    Route::post('/it_admin/suspend/{id}', [ITAdminController::class, 'suspend'])->name('it_admin.suspend');
    Route::put('/it_admin/update/{id}', [ITAdminController::class, 'update'])->name('it_admin.update');

});

/*
|--------------------------------------------------------------------------
| Approval Routes (Role: 2 - Supervisor, 3 - Admin, 4 - Comptroller)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:2', 'role:3', 'role:4'])->group(function () {
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });
});

/*
|--------------------------------------------------------------------------
| Purchase Routes (Role: 1 - Purchasing Officer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:1'])->group(function () {
    Route::resource('purchases', PurchaseController::class);
});

/*
|--------------------------------------------------------------------------
| Audit Trails (Role: 3 - Admins Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:3'])->group(function () {
    Route::get('audit-trails', [AuditTrailController::class, 'index'])->name('audit_trails.index');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/request/{id}', [AdminController::class, 'show'])->name('admin.show');
    Route::post('/admin/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');
});

/*
|--------------------------------------------------------------------------
| General Settings Route (Available to All Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/settings', function () {
    return view('settings');
})->name('settings');

/*
|--------------------------------------------------------------------------
| Password Change Routes (Available to All Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [PasswordController::class, 'showChangePasswordForm'])->name('change_password_form');
    Route::put('/change-password', [PasswordController::class, 'updatePassword'])->name('change_password');
});
