<?php

use Illuminate\Support\Facades\Route;

Route::resource('/servicedesk', 'ServicedeskController');
Route::get('ads', 'AdsController@index')->name('ads');
Route::resource('banned-links', 'BannedLinkController');
Route::get('feeds', 'FeedsController@index')->name('feeds');
Route::resource('links', 'LinksController')->only('index', 'create', 'delete', 'store');
Route::resource('news', 'NewsController')->only(['index', 'show']);
Route::resource('offers', 'OffersController')->only(['index', 'show']);
Route::get('orders/export/', 'OrdersController@export')->name('orders.export');
Route::resource('orders', 'OrdersController')->only(['index', 'show']);
Route::get('offers/materials/download', 'OffersController@download')->name('offers.materials.download');
Route::get('offers/{offer}/approve', 'OffersController@requestApprove')->name('offers.approve.approve');
Route::post('offers/{offer}/approve', 'OffersController@requestApproveStore')->name('offers.approve.request');
Route::get('payments', 'PaymentsController@index')->name('payments');
Route::get('postback', 'PostbackController@index')->name('postback');
Route::get('profile', 'ProfileController@index')->name('profile.index');
Route::put('profile/update/{user}', 'ProfileController@update')->name('users.update');
Route::get('report', 'ReportController@index')->name('report');
Route::get('report.download', 'ReportController@doExport')->name('report.download');
Route::resource('faq', 'FAQCategoriesController');
Route::get('postbacks', 'PostbacksController@index')->name('postbacks');
Route::post('postbacks', 'PostbacksController@store')->name('postbacks.store');
