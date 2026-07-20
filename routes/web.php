<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ServiceAccountController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/service-requests/create', [ServiceRequestController::class, 'create'])
    ->name('service-requests.create');
Route::post('/service-requests', [ServiceRequestController::class, 'store'])
    ->name('service-requests.store');

/* ============================================================
   เข้าสู่ระบบ / ออกจากระบบ (เปิดให้เข้าถึงได้โดยไม่ต้องล็อกอิน)
============================================================ */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,2');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/* ============================================================
   ส่วนของเจ้าหน้าที่ — ต้องล็อกอินก่อนเข้าถึง
============================================================ */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/requests', [ServiceAccountController::class, 'requestsIndex'])
        ->name('requests.index');

    Route::get('/requests/{serviceRequest}/accounts/create', [ServiceAccountController::class, 'createAccount'])
        ->name('accounts.create');

    Route::post('/requests/{serviceRequest}/accounts', [ServiceAccountController::class, 'storeAccount'])
        ->name('accounts.store');

    Route::get('/accounts', [ServiceAccountController::class, 'accountsIndex'])
        ->name('accounts.index');

    Route::get('/accounts/{account}/edit', [ServiceAccountController::class, 'editAccount'])
        ->name('accounts.edit');

    Route::put('/accounts/{account}', [ServiceAccountController::class, 'updateAccount'])
        ->name('accounts.update');

    Route::delete('/accounts/{account}', [ServiceAccountController::class, 'destroyAccount'])
        ->name('accounts.destroy');

    Route::patch('/accounts/{account}/toggle-status', [ServiceAccountController::class, 'toggleStatus'])
        ->name('accounts.toggle-status');

    Route::delete('/requests/{serviceRequest}', [ServiceAccountController::class, 'destroyRequest'])
        ->name('requests.destroy');

    Route::patch('/requests/{serviceRequest}/approve', [ServiceAccountController::class, 'approveRequest'])
        ->name('requests.approve');

    /* ---------- จัดการผู้ใช้เจ้าหน้าที่ ---------- */
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggle-active');
});
