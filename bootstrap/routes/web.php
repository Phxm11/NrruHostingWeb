<?php

use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Admin\ServiceAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/service-requests/create', [ServiceRequestController::class, 'create'])
    ->name('service-requests.create');
Route::post('/service-requests', [ServiceRequestController::class, 'store'])
    ->name('service-requests.store');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/requests', [ServiceAccountController::class, 'requestsIndex'])
        ->name('requests.index');

    Route::get('/requests/{serviceRequest}/accounts/create', [ServiceAccountController::class, 'createAccount'])
        ->name('accounts.create');

    Route::post('/requests/{serviceRequest}/accounts', [ServiceAccountController::class, 'storeAccount'])
        ->name('accounts.store');

    Route::get('/accounts', [ServiceAccountController::class, 'accountsIndex'])
        ->name('accounts.index');

    Route::patch('/accounts/{account}/toggle-status', [ServiceAccountController::class, 'toggleStatus'])
        ->name('accounts.toggle-status');
});