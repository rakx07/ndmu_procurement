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
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\PurchasingOfficerController;
use Illuminate\Support\Facades\Auth; // ✅ Fix: Added Auth facade

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
    if (Auth::check()) { // ✅ Fix: Use Auth facade
        switch (Auth::user()->role) {
            case 0:
                return redirect()->route('staff.dashboard'); // Staff
            case 1:
                return redirect()->route('purchasing_officer.dashboard');
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
Route::middleware(['auth', 'role:0'])->prefix('staff')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');

    // ✅ Procurement Request Management
    Route::get('/request/create', [ProcurementRequestController::class, 'create'])->name('staff.requests.create');
    Route::post('/request', [ProcurementRequestController::class, 'store'])->name('staff.requests.store');
    Route::get('/request/{id}/edit', [StaffController::class, 'edit'])->name('staff.requests.edit');
    Route::put('/request/{id}', [StaffController::class, 'update'])->name('staff.requests.update');

    // ✅ Procurement Requests - Viewing Only
    Route::get('/requests', [ProcurementRequestController::class, 'index'])->name('staff.requests.index');
    Route::get('/requests/{id}', [ProcurementRequestController::class, 'show'])->name('staff.requests.show');

    // ✅ Staff Can View Available Items But Not Add
    Route::get('/items', [ProcurementRequestController::class, 'availableItems'])->name('staff.items.index');
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
    Route::get('/supervisor/approved-requests', [SupervisorController::class, 'approvedRequests'])->name('supervisor.approved_requests');
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

    // ✅ IT Admin Settings Route (Fix)
    Route::get('/it-admin/settings', [ITAdminController::class, 'settings'])->name('it_admin.settings');
});



/*
|--------------------------------------------------------------------------
| Approval Routes (Restricted to Specific Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
});

/*
|--------------------------------------------------------------------------
| Purchase Routes (Role: 1 - Purchasing Officer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:1'])->prefix('purchasing-officer')->group(function () {
    Route::get('/dashboard', [PurchasingOfficerController::class, 'dashboard'])->name('purchasing_officer.dashboard');

    // ✅ Items Management
    Route::get('/items', [PurchasingOfficerController::class, 'index'])->name('purchasing_officer.items.index');
    Route::get('/items/create', [PurchasingOfficerController::class, 'create'])->name('purchasing_officer.items.create');
    Route::post('/items', [PurchasingOfficerController::class, 'store'])->name('purchasing_officer.items.store');
    Route::delete('/items/{id}', [PurchasingOfficerController::class, 'destroy'])->name('purchasing_officer.items.destroy');

    // ✅ Item Categories Management
    Route::get('/item-categories', [ItemCategoryController::class, 'index'])->name('item-categories.index');
    Route::post('/item-categories', [ItemCategoryController::class, 'store'])->name('item-categories.store');
    Route::delete('/item-categories/{id}', [ItemCategoryController::class, 'destroy'])->name('item-categories.destroy');
});

Route::middleware(['auth'])->get('/settings', function () {
    return view('it_admin.settings'); // Change this to the correct path of your settings view
})->name('settings');

