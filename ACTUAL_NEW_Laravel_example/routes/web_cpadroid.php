<?php

use Illuminate\Support\Facades\Route;

Route::any('confirm/16/cpapixel.gif', 'PixelController@cpapixel')->withoutMiddleware('web');
Route::any('confirm/16/clickpixel.gif', 'PixelController@clickpixel')->withoutMiddleware('web');
Route::any('confirm/16/fraudpixel.gif', 'PixelController@fraudpixel')->withoutMiddleware('web');
Route::get('trackad/{pp}.xml', 'Cloud\TrackAdController')->withoutMiddleware('web');
