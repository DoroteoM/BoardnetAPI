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
    Route::put('users/{user_id}', 'UserController@update');
    Route::delete('users/{user_id}', 'UserController@delete');

    //games
    Route::post('games/bgg', 'GameController@createFromLibrary');
    Route::get('games', 'GameController@readAll');
    Route::get('games/{bgg_game_id}', 'GameController@read');
    Route::put('games/{bgg_game_id}', 'GameController@update');
    Route::delete('games/{bgg_game_id}', 'GameController@delete');

    //libraries
    Route::post('libraries', 'LibraryController@create');
    Route::get('libraries/user/{username}', 'LibraryController@readByUser');
    Route::get('libraries/game/{bgg_game_id}', 'LibraryController@readByGame');
    Route::put('libraries/{library_id}', 'LibraryController@update');
    Route::delete('libraries/{library_id}', 'LibraryController@delete');
    Route::delete('libraries/user/{username}/game/{bgg_game_id}', 'LibraryController@deleteByUserAndGame');

    //friends
    Route::post('friends', 'FriendController@create');
    Route::get('friends/user/{username}', 'FriendController@readByUser');
    Route::get('friends/friend/{friend_username}', 'FriendController@readByFriend');
    Route::put('friends/{friends_id}', 'FriendController@update');
    Route::delete('friends/{friends_id}', 'FriendController@delete');
    Route::delete('friends/user/{username}/friend/{friend_username}', 'FriendController@deleteByUserAndFriend');


    //play
    Route::post('play', 'PlayController@create');
    Route::get('play/{play_id}', 'PlayController@read');
    Route::get('play/user/{play_id}', 'PlayController@readByUser');
    Route::put('play/{play_id}', 'PlayController@update');
    Route::delete('play/{play_id}', 'PlayConroller@delete');


    //test
    //Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
    //Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
});
