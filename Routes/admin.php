<?php

use Illuminate\Support\Facades\Route;
use Modules\CashOnDeliveryPayment\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| CashOnDeliveryPayment Admin Routes
|--------------------------------------------------------------------------
|
| Admin routes for cash on delivery payment settings.
|
*/

Route::prefix('modules/payment/cash-on-delivery-payment')->name('admin.payment.cod.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});