<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('', 'HomeController@index')->name('index')->middleware(['check_is_pp', 'guest']);

Route::get('/login_as_id/{user}', 'Auth\\LoginController@loginAsID')->name('login_as_id');

Auth::routes(['verify' => true]);
Route::impersonate();

Route::get('login_as', 'Auth\\LoginController@loginAs')->name('login_as');

Route::any('cpapixel.gif', 'PixelController@cloud')->withoutMiddleware('web')->middleware('check_is_pp');
Route::any('webhook/{webhookmethod?}', 'WebhookController')->middleware('check_is_pp');

Route::any('deploy_rQMzYzypyU6RU', 'DeployController@deploy');
Route::get('locale/{locale}', 'LocaleController')->name('locale');

// Автологин по ссылке из письма
Route::get('login/onetime/{token}', 'Auth\\LoginController@onetimelogin')->middleware('check_is_pp');
Route::get('login/by_token/{token}', 'Auth\\LoginController@tokenlogin')->middleware('check_is_pp');

Route::get('materials/{type}/{file}', 'StorageController@getMaterial');

Route::get('unsubscribe/{email}', 'UnsubscribeController')->name('unsubscribe');

Route::get('adv_api/{pp_id}', 'Advertiser\ApiController@index')->name('adv_api');
Route::get('api/orders', 'Partner\ApiOrdersController@orders')->name('api.orders');
Route::get('api/orders_paid', 'Partner\ApiOrdersController@orders_paid')->name('api.orders_paid');
