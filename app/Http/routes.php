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

Route::get('auth/{provider}', 'Auth\AuthController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback');

Route::group(['prefix' => 'users'], function () {
    Route::get('/{user}/{game}/cards', 'UsersController@user_cards');
});

Route::get('games/{game}/cards/search', 'CardsController@search');
Route::get('games/{game}/user_cards/{id}', 'CardsController@user_cards')->where('id', '[0-9]+');
Route::get('games/{game}/cards/{id}', 'CardsController@show')->where('id', '[0-9]+');
Route::get('games/{game}/cards/{id}/edit', 'CardsController@edit')->where('id', '[0-9]+');
Route::post('games/{game}/cards/update', 'CardsController@update');
Route::resource('games/{game}/cards/', 'CardsController');
Route::resource('games/{game}/decks/', 'DecksController');

Route::get('data/{game}/options', 'GameDataController@get_options');

Route::get('/', 'HomeController@index');

Route::auth();

Route::get('user/activation/{token}', 'Auth\AuthController@activateUser')->name('user.activate');
Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'testing'], function () {
    Route::get('{game}/cards/store', 'CardsController@store');
    Route::get('modals', 'TestController@modals');
});