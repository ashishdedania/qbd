<?php

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('import',  'ContactsController@import');
Route::post('import', 'ContactsController@parseImport');

Route::post('upload', 'ContactsController@upload');

Route::get('/getEventStream', 'ContactsController@getEventStream');
Route::get('/getProcessData', 'ContactsController@getProcessData');
