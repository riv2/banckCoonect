<?php

use Illuminate\Support\Facades\Route;

Route::get('report', 'ReportController@index')->name('report');
Route::get('profile', 'ProfileController@index')->name('profile');
Route::get('orders/export/', 'OrdersController@export')->name('orders.export');
Route::resource('orders', 'OrdersController');
Route::resource('offers', 'OffersController')->only('index', 'show');
Route::resource('orders.products', 'OrdersProductsController');
