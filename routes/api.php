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

    //registracija i login
    Route::post('auth/register', 'Auth\ApiRegisterController@register');
    Route::post('auth/login', 'Auth\ApiAuthController@login');

    //user
    Route::get('users/{username}', 'UserController@read');
    Route::put('users/{user_id}', 'UserController@update');
    Route::delete('users/{user_id}', 'UserController@delete');

    //game
    Route::post('games/bgg', 'GameController@createFromLibrary');
    Route::get('games', 'GameController@readAll');
    Route::get('games/{game_id}', 'GameController@read');
    Route::delete('games/{game_id}', 'GameController@delete');

    //test
    //Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
    //Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
});
