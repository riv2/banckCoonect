<?php

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

Auth::routes();

//vrnext routes
Route::any('/', 'VrnextController@index')->name('vrnext');

Route::any('/dashboard', 'VrnextController@index')->name('dashboard');
Route::any('/recovery', 'VrnextController@index')->name('recovery');
Route::any('/api', 'VrnextController@index')->name('api');
Route::any('/myplan', 'VrnextController@index')->name('myplan');
Route::any('/app', 'VrnextController@index')->name('app');

Route::any('/ceramic', 'VrnextController@index')->name('ceramic');
Route::any('/wallpapers', 'VrnextController@index')->name('wallpapers');
Route::any('/laminate', 'VrnextController@index')->name('laminate');
Route::any('/paints', 'VrnextController@index')->name('paints');

Route::any('/companies', 'VrnextController@index')->name('companies');
Route::any('/members', 'VrnextController@index')->name('members');
Route::any('/groups', 'VrnextController@index')->name('groups');
Route::any('/permissions', 'VrnextController@index')->name('permissions');
Route::any('/catalogues', 'VrnextController@index')->name('catalogues');
Route::any('/tokens', 'VrnextController@index')->name('tokens');

Route::any('/subsribes', 'VrnextController@index')->name('subsribes');
Route::any('/membership', 'VrnextController@index')->name('membership');

Route::any('/signout', 'VrnextController@index')->name('signout');
Route::any('/signin', 'VrnextController@index')->name('signin');
Route::any('/account', 'VrnextController@index')->name('account');
Route::any('/signup', 'VrnextController@index')->name('signup');
Route::any('/currencies', 'VrnextController@index')->name('currencies');
Route::any('/gateways', 'VrnextController@index')->name('gateways');
Route::any('/products', 'VrnextController@index')->name('products');
Route::any('/mycompany', 'VrnextController@index')->name('mycompany');


Route::any('/signup', 'VrnextController@index')->name('signup');

Route::any('/robokassa/result', 'GatewaysController@robokassaResult')->name('robokassaresult');
Route::any('/robokassa/success', 'GatewaysController@robokassaSuccess')->name('robokassasuccess');
Route::any('/robokassa/fail', 'GatewaysController@robokassaFail')->name('robokassafail');

Route::any('/log', 'VrnextController@log')->name('log');


