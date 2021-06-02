<?php

use Illuminate\Support\Facades\Route;

Route::get('', 'HomeController@index')->name('index')->middleware('guest');
Route::get('/already_registered', 'HomeController@already_registered')->name('already.registered');
