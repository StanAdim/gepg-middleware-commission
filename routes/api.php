<?php

use App\Http\Controllers\Api\V1\BillController;
use App\Http\Controllers\Api\V1\GEPGResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('bill')
        ->middleware('auth:sanctum')
        ->controller(BillController::class)
        ->group(function () {
            Route::post('', 'store');
        });

    Route::prefix('bill-response')
        ->controller(GEPGResponseController::class)
        ->group(function () {
            Route::post('control-no', 'controlNoResponse');
            Route::post('payment-receipt', 'paymentReceipt');
            Route::post('reconcile-receipt', 'reconcileReceipt');
        });

    /*  Route::resource('bill', BillController::class)
         ->middleware('auth:sanctum'); */
});

