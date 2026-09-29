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

use Illuminate\Support\Facades\Route;

Route::prefix('hr')->middleware(['auth'])->group(function() {
	Route::prefix('role-permission')->group(function() {
	    Route::name('permission.')->group(function() {
	    	Route::middleware('permission')->group(function(){
	    		Route::get('roles/{id}/delete', 'RoleController@destroy')->name('roles.delete');
				Route::resource('roles', 'RoleController');
		        Route::resource('permissions', 'PermissionController');
	    	});
	        
	    });
	});

    Route::post('user-table-column-update-self/{id}', 'UserColumnPermissionController@update_self')->name('user_column_permission.update_self');
    Route::post('user-table-column-store-self', 'UserColumnPermissionController@store_self')->name('user_column_permission.store_self');
    Route::post('user-table-column-show-for-me/{table_name}', 'UserColumnPermissionController@show_with_self')->name('user_column_permission.show_with_self');
});

Route::post('/role-user','RoleController@roleUsers')->name('get.role.users');
