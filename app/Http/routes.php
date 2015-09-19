<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['namespace'     => 'Api', 'prefix' => 'api'], function () {
    Route::group(['namespace' => 'V1', 'prefix' => 'v1'], function () {
        Route::get('torrents', ['as' => 'torrent/search', 'uses' => 'Torrents@search']);
        Route::get('torrents/{hash}', ['as' => 'torrent/get', 'uses' => 'Torrents@get']);
    });
});
