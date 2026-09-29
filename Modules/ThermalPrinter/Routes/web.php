<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'thermalprinter', 'middleware' => ['web', 'auth'], 'as' => 'thermalprinter.'], function () {
    Route::get('/settings', 'ThermalPrinterController@settings')->name('settings');
    Route::post('/save-settings', 'ThermalPrinterController@saveSettings')->name('saveSettings');

    Route::get('/print/full-invoice/{order_id}', 'ThermalPrinterController@printInvoice')->name('printInvoice');
    Route::post('/print/get-order-data', 'ThermalPrinterController@getOrderDataForPrinting')->name('getOrderData');
});
