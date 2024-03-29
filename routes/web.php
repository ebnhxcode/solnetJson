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
Route::post('/rest/api/post', 'FileMakerApiRestController@postDataRequestByLayout');

#Real routes :

Route::get('rest/api/all/{layout}', 'FileMakerApiRestController@all'); #Head
Route::get('rest/api/get/{layout}/{record_id}', 'FileMakerApiRestController@get'); #Get
Route::post('rest/api/edit', 'FileMakerApiRestController@edit'); #Edit record with recordId
Route::post('rest/api/find', 'FileMakerApiRestController@find'); #Get Shortcut for find
Route::post('rest/api/post/{layout}/{record_id}', 'FileMakerApiRestController@post'); #Create, save

Route::post('rest/api/save', 'FileMakerApiRestController@save'); #Create, save, shortcut of post

Route::post('rest/api/store/{layout}/{record_id}', 'FileMakerApiRestController@store'); #Create, save, shortcut of post
Route::post('rest/api/put', 'FileMakerApiRestController@put'); #Update
Route::post('rest/api/update/{layout}/{record_id}', 'FileMakerApiRestController@update'); #Update, shortcut of put
Route::post('rest/api/delete/{layout}/{record_id}', 'FileMakerApiRestController@delete'); #Delete





#Auth::routes();
#Route::get('/home', 'HomeController@index')->name('home');
