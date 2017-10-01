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

#Route::get('/', function () { return view('welcome'); });

#Route to test connection with FM Api rest
#Route::get('test', 'TestController@test');


#For the moment all is public : mode development.
Route::get('/test/{type}', 'FileMakerApiRestController@test');
Route::get('/auth/login', 'FileMakerApiRestController@login');
Route::get('/auth/logout', 'FileMakerApiRestController@logout');
Route::get('rest/api/get/{layout}', 'FileMakerApiRestController@getDataRequestByLayout');





#Auth::routes();
#Route::get('/home', 'HomeController@index')->name('home');
