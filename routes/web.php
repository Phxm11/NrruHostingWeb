<?php

use App\Http\Controllers\ServiceRequestController;
use Illuminate\Support\Facades\Route;


Route::get('/service-requests/create', [ServiceRequestController::class, 'create'])
    ->name('service-requests.create');

Route::post('/service-requests', [ServiceRequestController::class, 'store'])
    ->name('service-requests.store');

