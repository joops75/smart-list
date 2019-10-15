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

Route::resource('project', 'ProjectController')->middleware('auth');

Auth::routes();

Route::resource('task', 'TaskController')->only([
    'store', 'update', 'destroy'
])->middleware('auth');

Route::get('event', 'EventController@getEvents');

Route::delete('event', 'EventController@deleteEvents');
