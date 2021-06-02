<?php

use Illuminate\Support\Facades\Route;

Route::middleware('welcome')->group(function () {
    Route::resource('servicedesk', 'ServicedeskController');
    Route::resource('servicedesk.comments', 'ServicedeskCommentsController');
    Route::resource('servicedeskadv', 'ServicedeskAdvController');
    Route::as('.servicedesk')->resource('/servicedesk/templates', 'ServicedeskTemplatesController');

    Route::get('account', 'AccountController@index')->name('account');
    Route::get('banned-frauds/export', 'BannedFraudController@export')->name('banned-frauds.export');
    Route::resource('banned-frauds', 'BannedFraudController');
    Route::resource('banned-links', 'BannedLinkController');
    Route::resource('news', 'NewsController');
    Route::get('news/{news}/send', 'NewsController@send')->name('news.send');
    Route::resource('offers', 'OffersController');


    Route::get('offers/materials', 'OffersMaterialsController@index')->name('offers.materials');
    Route::get('offers/materials/delete/{offer_material_id}', 'OffersMaterialsController@delete')->name('offers.materials.delete');
    Route::get('offers/materials/edit', 'OffersMaterialsController@edit')->name('offers.materials.edit');
    Route::post('offers/materials/edit', 'OffersMaterialsController@update')->name('offers.materials.edit');
    Route::get('offers/materials/new', 'OffersMaterialsController@create')->name('offers.materials.new');
    Route::post('offers/materials/new', 'OffersMaterialsController@store')->name('offers.materials.store');
    Route::resource('rateRule', 'RateRuleController');
    Route::get('orders/import', 'OrdersController@importForm')->name('orders.import');
    Route::put('orders/import', 'OrdersController@import')->name('orders.import');
    Route::get('orders/export', 'OrdersController@export')->name('orders.export');
    Route::resource('orders', 'OrdersController');
    Route::resource('orders.products', 'OrdersProductsController');
    Route::resource('reestrs', 'ReestrsController')->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('reestrs-export/{reestr_id}', 'ReestrsController@export')->name('reestrs.export');
    Route::post('reestrs-stop-update', 'ReestrsController@stopUpdate')->name('reestrs.stop-update');
    Route::post('reestrs-start-update', 'ReestrsController@startUpdate')->name('reestrs.start-update');
    Route::get('postbacks', 'PostbacksController@index')->name('postbacks');
    Route::resource('penaltys', 'PenaltysController');
    Route::get('tariff', 'TariffController@index')->name('tariff');

    Route::group([
        'prefix' => 'integration',
        'as' => 'integration.'
    ], function () {
        Route::get('cms', 'CmsViewController@index')->name('cms');
        Route::get('cms/tilda/{projectid?}', 'CmsViewController@tilda')->name('cms.tilda');
        Route::post('cms/tilda', 'CmsViewController@tilda')->name('cms.tilda');
        Route::get('api', 'ApiViewController@index')->name('api');
        Route::get('pixel', 'PixelViewController@index')->name('pixel');
    });

    Route::get('report', 'ReportController@index')->name('report');
    Route::get('report.download', 'ReportController@doExport')->name('report.download');

    Route::group([
        'prefix' => 'settings',
        'namespace' => 'Settings',
        'as' => 'settings.'
    ], function () {
        Route::get('appearance', 'AppearanceController@index')->name('appearance.index');
        Route::put('appearance', 'AppearanceController@update')->name('appearance.update');
        Route::get('company', 'CompanyController@index')->name('company.index');
        Route::put('company', 'CompanyController@update')->name('company.update');
        Route::resource('faq', 'FAQCategoriesController');
        Route::resource('faq.category', 'FAQController');
    });

    Route::post('orders/create', 'OrdersController@store')->name('orders.create');
    Route::post('orders/recalc', 'OrdersController@recalc')->name('orders.recalc');

    Route::group([], function () {
        Route::resource('partners', 'PartnersController')->parameters(['partners' => 'user'])->only(['index', 'show', 'create']);
        Route::resource('users', 'PartnersController')->only(['show', 'update', 'store']);
    });

    Route::get('welcome', 'WelcomeController@index')->name('welcome.index');
    Route::put('welcome', 'WelcomeController@store')->name('welcome.store');
});
