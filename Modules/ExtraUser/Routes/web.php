<?php

use Illuminate\Support\Facades\Route;

Route::prefix('contact')->group(function () {

    Route::get("/agencies", "ExtraUserController@agencies")->name("agencies.index");
    Route::get("/strategic-partner", "ExtraUserController@strategicPartner")->name("strategic-partner.index");
    Route::get("/benches", "ExtraUserController@benches")->name("benches.index");
    Route::get("/kiosks", "ExtraUserController@kiosks")->name("kiosks.index");
    Route::get("contact/{id}/show", "ExtraUserController@agencie_details")->name("contact.view");

    Route::get("extrauser/create", "ExtraUserController@create")->name("extrauser.create");
    Route::post("extrauser/create", "ExtraUserController@store");

    Route::get("extrauser/{contact}/edit", "ExtraUserController@edit")->name("extrauser.edit");
    Route::put("extrauser/{contact}", "ExtraUserController@update")->name("extrauser.update");

    Route::get("extrauser/{contact}", "ExtraUserController@show")->name("extrauser.show");

   /* Route::get("/customer/search", "ContactController@customer")->name("customer.search_index");
    Route::get("/customer/details/{id}", "ContactController@customer_details")->name("customer.view");

    Route::middleware('permission')->group(function(){
        Route::get("/supplier", "ContactController@supplier")->name("supplier");
        Route::get("/customer", "ContactController@customer")->name("customer");
        Route::get('/add_contact/{id}/delete', 'ContactController@destroy')->name('add_contact.delete');

    });*/
});
