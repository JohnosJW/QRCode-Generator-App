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

    Route::resource('accounts', 'AccountController')->except(['show']);

    Route::get('/accounts/show/{id?}', 'AccountController@show')->name('accounts.show');

    Route::resource('accountHistories', 'AccountHistoryController');

    Route::resource('qrcodes', 'QrcodeController')->except(['show']);

    Route::resource('transactions', 'TransactionController');

    Route::resource('users', 'UserController');

    Route::group(['middleware' => 'check.moderator'], function () {
        Route::get('/users', 'UserController@index')->name('users.index');
    });

    Route::resource('roles', 'RoleController')->middleware('check.admin');

    Route::post('/accounts/apply_for_payout', 'AccountController@applyForPayout')->name('accounts.apply_for_payout');

    Route::post('/accounts/mark_as_paid', 'AccountController@markAsPaid')
        ->name('accounts.mark_as_paid')
        ->middleware('check.moderator');

    Route::get('/accounts', 'AccountController@index')
        ->name('accounts.index')
        ->middleware('check.moderator');

    Route::get('accounts/create', 'AccountController@create')
        ->name('accounts.create')
        ->middleware('check.admin');


    Route::get('/accountHistories', 'AccountHistoryController@index')
        ->name('accountHistories.index')
        ->middleware('check.moderator');

    Route::get('accountHistories/create', 'AccountHistoryController@create')
        ->name('accountHistories.create')
        ->middleware('check.admin');

});

Route::get('/qrcodes/{id}', 'QrcodeController@show')->name('qrcodes.show');