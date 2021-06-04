<?php

use AvtoDev\JsonRpc\RpcRouter;
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

RpcRouter::on('balance.userBalance', 'App\Http\Controllers\Balance\BalanceController@userBalance');
RpcRouter::on('balance.history',     'App\Http\Controllers\Balance\BalanceController@history');

Route::post('/', 'AvtoDev\JsonRpc\Http\Controllers\RpcController');
