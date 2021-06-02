<?php

use Illuminate\Support\Facades\Route;

Route::as('.servicedesk')->resource('/servicedesk/templates', 'ServicedeskTemplatesController');
Route::resource('servicedesk', 'ServicedeskController');
Route::resource('servicedesk.comments', 'ServicedeskCommentsController');
Route::group([], function () {
    Route::resource('advertisers', 'AdvertisersController')->parameters(['advertisers' => 'user'])->only(['index', 'show', 'create']);
    Route::resource('analysts', 'AnalystsController')->parameters(['analysts' => 'user'])->only(['index', 'show']);
    Route::resource('partners', 'PartnersController')->parameters(['partners' => 'user'])->only(['index', 'show']);
    Route::resource('users', 'PartnersController')->only(['show', 'update', 'store']);
});

Route::get('report', 'ReportController@index')->name('report');
Route::get('pixel', 'PixelController@index')->name('pixel.index');
Route::post('pixel', 'PixelController@recalc')->name('pixel.recalc');
Route::get('payments', 'PaymentsController@index')->name('payments');
Route::get('blocks', 'BlocksController@index')->name('blocks');
Route::get('postbacks', 'PostbacksController@index')->name('postbacks');
Route::get('offers', 'OffersController@index')->name('offers.index');
Route::get('fees', 'FeesController@index')->name('fees');
Route::resource('news', 'NewsController');
Route::get('news/{news}/send', 'NewsController@send')->name('news.send');
Route::get('ads', 'AdsController@index')->name('ads');
Route::get('reestrs', 'ReestrsController@index')->name('reestrs');
Route::get('import', 'ImportController@index')->name('import');
Route::get('traffic_sources/', 'TrafficSourcesController@index')->name('traffic.sources');
Route::get('traffic_sources/new', 'TrafficSourcesController@create')->name('traffic.sources.new');
Route::post('traffic_sources/save', 'TrafficSourcesController@save')->name('traffic.sources.save');
Route::get('traffic_sources/edit', 'TrafficSourcesController@edit')->name('traffic.sources.edit');
