<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/contracts/{contract}/invoices', [InvoiceController::class, 'store']);
    Route::get('/contracts/{contract}/invoices', [InvoiceController::class, 'index']);
    Route::get('/contracts/{contract}/summary', [InvoiceController::class, 'summary']);
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
