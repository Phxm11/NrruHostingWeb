<?php

use App\Http\Controllers\Admin\ServiceAccountController;
use Illuminate\Support\Facades\Route;

// ===== เพิ่มบล็อกนี้ต่อท้ายไฟล์ routes/web.php เดิม =====
Route::prefix('admin')->name('admin.')->group(function () {

    // รายการคำขอใช้บริการทั้งหมด (หน้าแรกฝั่งเจ้าหน้าที่)
    Route::get('/requests', [ServiceAccountController::class, 'requestsIndex'])
        ->name('requests.index');

    // ฟอร์มสร้างบัญชี Username/Password ให้คำขอที่เลือก
    Route::get('/requests/{serviceRequest}/accounts/create', [ServiceAccountController::class, 'createAccount'])
        ->name('accounts.create');

    Route::post('/requests/{serviceRequest}/accounts', [ServiceAccountController::class, 'storeAccount'])
        ->name('accounts.store');

    // รายการบัญชีทั้งหมดที่สร้างไปแล้ว
    Route::get('/accounts', [ServiceAccountController::class, 'accountsIndex'])
        ->name('accounts.index');

    // เปิด/ปิดใช้งานบัญชี
    Route::patch('/accounts/{account}/toggle-status', [ServiceAccountController::class, 'toggleStatus'])
        ->name('accounts.toggle-status');
});
