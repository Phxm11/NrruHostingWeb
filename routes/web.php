<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceAccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DomainController;
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

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,2')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,2')
        ->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/* ============================================================
   ส่วนของเจ้าหน้าที่ — ต้องล็อกอินก่อนเข้าถึง
   แก้ไข: เดิมมีแค่ middleware('auth') ตัวเดียว ทั้งที่ roles/permissions มีข้อมูลสิทธิ์
   ครบอยู่แล้วใน DB (ดู PermissionController/RoleController) แต่ไม่เคยถูกใช้บังคับ route จริง
   ตอนนี้เพิ่ม middleware('permission:xxx') ต่อท้ายแต่ละ route ตามชื่อ permission ที่ seed ไว้แล้ว
   (permissions.view / requests.approve ฯลฯ — ดูตาราง permissions)
   หมายเหตุ: หน้า domains.* ไม่มี permission เฉพาะของตัวเอง จึงใช้ accounts.* ร่วม
   เพราะ "โดเมน" ผูกอยู่กับ "บัญชีผู้ขอใช้บริการ" อยู่แล้วในเชิงข้อมูล
============================================================ */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/requests', [ServiceAccountController::class, 'requestsIndex'])
        ->middleware('permission:requests.view')
        ->name('requests.index');

    Route::get('/requests/{serviceRequest}', [ServiceAccountController::class, 'show'])
        ->middleware('permission:requests.view')
        ->name('requests.show');

    Route::get('/requests/{serviceRequest}/accounts/create', [ServiceAccountController::class, 'createAccount'])
        ->middleware('permission:accounts.create')
        ->name('accounts.create');

    Route::post('/requests/{serviceRequest}/accounts', [ServiceAccountController::class, 'storeAccount'])
        ->middleware('permission:accounts.create')
        ->name('accounts.store');

    Route::get('/accounts', [ServiceAccountController::class, 'accountsIndex'])
        ->middleware('permission:accounts.view')
        ->name('accounts.index');

    Route::get('/accounts/{account}/edit', [ServiceAccountController::class, 'editAccount'])
        ->middleware('permission:accounts.edit')
        ->name('accounts.edit');

    Route::put('/accounts/{account}', [ServiceAccountController::class, 'updateAccount'])
        ->middleware('permission:accounts.edit')
        ->name('accounts.update');

    Route::delete('/accounts/{account}', [ServiceAccountController::class, 'destroyAccount'])
        ->middleware('permission:accounts.delete')
        ->name('accounts.destroy');

    Route::patch('/accounts/{account}/toggle-status', [ServiceAccountController::class, 'toggleStatus'])
        ->middleware('permission:accounts.toggle')
        ->name('accounts.toggle-status');

    /* ---------- โดเมนของผู้ใช้บริการ ---------- */
    Route::get('/domains', [DomainController::class, 'index'])
        ->middleware('permission:accounts.view')
        ->name('domains.index');
    Route::get('/domains/{domain}', [DomainController::class, 'show'])
        ->middleware('permission:accounts.view')
        ->name('domains.show');
    Route::get('/domains/{domain}/edit', [DomainController::class, 'edit'])
        ->middleware('permission:accounts.edit')
        ->name('domains.edit');
    Route::put('/domains/{domain}', [DomainController::class, 'update'])
        ->middleware('permission:accounts.edit')
        ->name('domains.update');
    Route::delete('/domains/{domain}', [DomainController::class, 'destroy'])
        ->middleware('permission:accounts.delete')
        ->name('domains.destroy');

    Route::delete('/requests/{serviceRequest}', [ServiceAccountController::class, 'destroyRequest'])
        ->middleware('permission:requests.delete')
        ->name('requests.destroy');

    Route::patch('/requests/{serviceRequest}/approve', [ServiceAccountController::class, 'approveRequest'])
        ->middleware('permission:requests.approve')
        ->name('requests.approve');

    /* ---------- จัดการผู้ใช้เจ้าหน้าที่ ---------- */
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.edit')
        ->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.edit')
        ->name('users.update');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->middleware('permission:users.toggle')
        ->name('users.toggle-active');

    /* ---------- จัดการบทบาท ---------- */
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:roles.create')
        ->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.create')
        ->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:roles.edit')
        ->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.edit')
        ->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:roles.delete')
        ->name('roles.destroy');

    /* ---------- จัดการสิทธิ์ ---------- */
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permissions.view')
        ->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->middleware('permission:permissions.create')
        ->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])
        ->middleware('permission:permissions.create')
        ->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->middleware('permission:permissions.edit')
        ->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
        ->middleware('permission:permissions.edit')
        ->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('permission:permissions.delete')
        ->name('permissions.destroy');
});
