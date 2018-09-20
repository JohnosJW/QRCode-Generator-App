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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {

    Route::resource('accounts', 'AccountController');

    Route::resource('accountHistories', 'AccountHistoryController');

    Route::resource('qrcodes', 'QrcodeController');

    Route::resource('transactions', 'TransactionController');

    Route::resource('users', 'UserController');

    Route::group(['middleware' => 'check.moderator'], function () {
        Route::get('/users', 'UserController@index')->name('users.index');
    });

    Route::resource('roles', 'RoleController')->middleware('check.admin');

//    Route::resource('users', 'UserController')->middleware('check.moderator');

});

Route::resource('accounts', 'AccountController');