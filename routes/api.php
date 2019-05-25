<?php


use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['api','cors']], function () {

    //registration i login
    Route::post('auth/register', 'Auth\ApiRegisterController@register');
    Route::post('auth/login', 'Auth\ApiAuthController@login');

    //users
    Route::get('users/{username}', 'UserController@read');
    Route::put('users/{userId}', 'UserController@update');
    Route::delete('users/{userId}', 'UserController@delete');

    //games
    Route::post('games/bgg', 'GameController@createFromLibrary');
    Route::get('games', 'GameController@readAll');
    Route::get('games/{bggGameId}', 'GameController@read');
    Route::put('games/{bggGameId}', 'GameController@update');
    Route::delete('games/{bggGameId}', 'GameController@delete');

    //libraries
    Route::post('libraries', 'LibraryController@create');
    Route::get('libraries/user/{username}', 'LibraryController@readByUser');
    Route::get('libraries/game/{bggGameId}', 'LibraryController@readByGame');
    Route::put('libraries/{libraryId}', 'LibraryController@update');
    Route::delete('libraries/{libraryId}', 'LibraryController@delete');

    //test
    //Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
    //Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
});
