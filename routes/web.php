<?php

use Illuminate\Support\Facades\Route;
use JiagBrody\LaravelFacturaMx\Http\Controllers\InvoiceController;

// Route::get('laravel-factura-mx', [\App\Http\Controllers\TestController::class, 'testing']);

// Route::group(['namespace' => 'laravel-factura-mx\Controllers'], function () {
//     Route::get('stuff', ['uses' => 'StuffController@index']);
// });
// Route::get('factura-mx', function () {
//
// });

Route::prefix('laravel-factura-mx')->name('laravel-factura-mx.')->group(function () {
    // Route::group(['namespace' => 'laravel-factura-mx\Controllers'], function () {
    // Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::resource('invoices', InvoiceController::class);
    // Route::get('laravel-factura-mx', ['uses' => 'StuffController@index']);
    // });
});
