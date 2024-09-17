<?php

use Illuminate\Support\Facades\Route;
use JiagBrody\LaravelFacturaMx\Http\Controllers\InvoiceController;
use JiagBrody\LaravelFacturaMx\Http\Middleware\HandleInertiaRequests;

// Route::get('laravel-factura-mx', [\App\Http\Controllers\TestController::class, 'testing']);

// Route::group(['namespace' => 'laravel-factura-mx\Controllers'], function () {
//     Route::get('stuff', ['uses' => 'StuffController@index']);
// });
// Route::get('factura-mx', function () {
//
// });

Route::middleware(['web', HandleInertiaRequests::class])->prefix('laravel-factura-mx')->name('laravel-factura-mx.')->group(function () {
    // Route::group(['namespace' => 'laravel-factura-mx\Controllers'], function () {
    // Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/get-status', [InvoiceController::class, 'getStatus'])->name('invoices.status');
    Route::get('invoices/{invoice}/get-cancel-data', [InvoiceController::class, 'getCancelData'])->name('invoices.get-cancel-data');
    Route::delete('invoices/{invoice}/set-cancel', [InvoiceController::class, 'SetCancel'])->name('invoices.set-cancel');
});
