<?php

use App\Http\Controllers\Api\V1\BillController;
use App\Http\Controllers\Api\V1\GEPGResponseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('billing')
        ->middleware(['auth:sanctum', 'ability:' . User::ABILITY_UPDATE_BILLING_INFO])
        ->controller(BillController::class)
        ->group(function () {
            Route::post('', 'store');
        });

    /* Route::prefix('bill')
        ->controller(GEPGResponseController::class)
        ->middleware(['auth:sanctum', 'ability:' . User::ABILITY_UPDATE_GEPG_INFO])
        ->group(function () {
            Route::post('receive-controll', 'controlNoResponse');
            Route::post('receive-payment', 'paymentReceipt');
            Route::post('reconciliation-response', 'reconcileReceipt');
        }); */

});
Route::get('/test', function (Request $request) {
    return 'Yes!, API is Live!'. $request->name;
});
Route::prefix('bill')
    ->controller(GEPGResponseController::class)
    ->group(function () {
        Route::post('receive-controll', 'controlNoResponse');
        Route::post('receive-payment', 'paymentReceipt');
        Route::post('reconciliation-response', 'reconcileReceipt');
    });
