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

Route::group(['middleware' => ['api', 'cors']], function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', 'Auth\ApiRegisterController@register');
        Route::post('login', 'Auth\ApiAuthController@login');
    });

    Route::prefix('users')->group(function () {
        Route::get('', 'UserController@index');
        Route::get('{user_id}', 'UserController@show')->where('user_id', '[0-9]+');
        Route::put('{user_id}', 'UserController@update')->where('user_id', '[0-9]+');
        Route::delete('{user_id}', 'UserController@destroy')->where('user_id', '[0-9]+');
        Route::get('{username}', 'UserController@showByUsername');
        Route::get('search/name/{name}', 'UserController@searchByName');
        Route::get('search/username/{username}', 'UserController@searchByUsername');
    });

    Route::prefix('games')->group(function () {
        Route::get('', 'GameController@index');
        Route::get('{game_id}/{username?}', 'GameController@showByBggId')->where('game_id', '[0-9]+');
        Route::put('{game_id}', 'GameController@update')->where('game_id', '[0-9]+');
        Route::delete('{game_id}', 'GameController@destroy')->where('game_id', '[0-9]+');
        Route::get('{bgg_game_id}/{username?}', 'GameController@showByBggId')->where('bgg_game_id', '[0-9]+');
        Route::put('{bgg_game_id}', 'GameController@updateByBggId')->where('bgg_game_id', '[0-9]+');
        Route::delete('{bgg_game_id}', 'GameController@destroyByBggId')->where('bgg_game_id', '[0-9]+');
        Route::post('bgg', 'GameController@storeFromLibrary');
        Route::get('search/name/{name}', 'GameController@searchByName');
        Route::get('search/letter/{letter}', 'GameController@searchByLetter');
    });

    Route::prefix('libraries')->group(function () {
        Route::get('', 'LibraryController@index');
        Route::post('', 'LibraryController@store');
        Route::get('{library_id}', 'LibraryController@show')->where('library_id', '[0-9]+');
        Route::put('{library_id}', 'LibraryController@update')->where('library_id', '[0-9]+');
        Route::delete('{library_id}', 'LibraryController@destroy')->where('library_id', '[0-9]+');
        Route::get('user/{username}', 'LibraryController@showByUser');
        Route::get('game/{bgg_game_id}', 'LibraryController@showByBggId')->where('bgg_game_id', '[0-9]+');
        Route::delete('user/{username}/game/{bgg_game_id}', 'LibraryController@destroyByUserAndGame')->where('bgg_game_id', '[0-9]+');
    });

    Route::prefix('friends')->group(function () {
        Route::get('', 'FriendController@index');
        Route::post('', 'FriendController@store');
        Route::get('{friends_id}', 'FriendController@show')->where('friends_id', '[0-9]+');
        Route::put('{friends_id}', 'FriendController@update')->where('friends_id', '[0-9]+');
        Route::delete('{friends_id}', 'FriendController@destroy')->where('friends_id', '[0-9]+');
        Route::get('user/{username}', 'FriendController@showByUser');
        Route::get('friend/{friend_username}', 'FriendController@showByFriend');
        Route::get('are-friends/user/{username}/friend/{friend_username}', 'FriendController@areFriends');
        Route::delete('user/{username}/friend/{friend_username}', 'FriendController@deleteByUserAndFriend');
    });

    Route::prefix('play')->group(function () {
        Route::get('', 'PlayController@index');
        Route::post('', 'PlayController@store');
        Route::get('{play_id}', 'PlayController@show')->where('play_id', '[0-9]+');
        Route::put('{play_id}', 'PlayController@update')->where('play_id', '[0-9]+');
        Route::delete('{play_id}', 'PlayController@destroy')->where('play_id', '[0-9]+');
        Route::get('friends-not-in-play/{play_id}', 'PlayController@showFriendsNotInPlay')->where('play_id', '[0-9]+');
        Route::get('user/{username}', 'PlayController@showByUser');
    });

    Route::prefix('team')->group(function () {
        Route::get('', 'TeamController@index');
        Route::post('', 'TeamController@store');
        Route::get('{team_id}', 'TeamController@show')->where('team_id', '[0-9]+');
        Route::put('{team_id}', 'TeamController@update')->where('team_id', '[0-9]+');
        Route::delete('{team_id}', 'TeamController@destroy')->where('team_id', '[0-9]+');
        Route::get('play/{play_id}', 'TeamController@showByPlay')->where('play_id', '[0-9]+');
    });

    Route::prefix('player')->group(function () {
        Route::get('', 'PlayerController@index');
        Route::post('', 'PlayerController@store');
        Route::get('{player_id}', 'PlayerController@show')->where('player_id', '[0-9]+');
        Route::put('{player_id}', 'PlayerController@update')->where('player_id', '[0-9]+');
        Route::delete('{player_id}', 'PlayerController@destroy')->where('player_id', '[0-9]+');
        Route::get('play/{play_id}', 'PlayerController@showByPlay')->where('play_id', '[0-9]+');
        Route::get('team/{play_id}', 'PlayerController@showByTeam')->where('play_id', '[0-9]+');
    });

    //test
//    Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
//    Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
//    Route::post('test/request', 'TestController@request');
});
