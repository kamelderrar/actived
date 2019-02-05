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

Route::get('/', function () {
    return view('welcome');
});

Route::get('facture/status', 'FactureController@getAvailableFactureStatus')->middleware('cors');
Route::get('facture/{page}', 'FactureController@index')->middleware('cors');
Route::post('facture', 'FactureController@index')->middleware('cors');
Route::get('facture/delete/{id}', 'FactureController@delete')->middleware('cors');