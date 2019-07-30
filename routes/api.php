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
    Route::get('users', 'UserController@index');
    Route::get('users/{user_id}', 'UserController@show')->where('user_id','[0-9]+');
    Route::put('users/{user_id}', 'UserController@update')->where('user_id','[0-9]+');
    Route::delete('users/{user_id}', 'UserController@destroy')->where('user_id','[0-9]+');
    Route::get('users/{username}', 'UserController@showByUsername');
    Route::get('users/search/name/{name}', 'UserController@searchByName');
    Route::get('users/search/username/{username}', 'UserController@searchByUsername');

    //games
    Route::get('games', 'GameController@index');
    Route::get('games/{game_id}/{username?}', 'GameController@showByBggId')->where('game_id','[0-9]+');
    Route::put('games/{game_id}', 'GameController@update')->where('game_id','[0-9]+');
    Route::delete('games/{game_id}', 'GameController@destroy')->where('game_id','[0-9]+');
    Route::get('games/{bgg_game_id}/{username?}', 'GameController@showByBggId')->where('bgg_game_id','[0-9]+');
    Route::put('games/{bgg_game_id}', 'GameController@updateByBggId')->where('bgg_game_id','[0-9]+');
    Route::delete('games/{bgg_game_id}', 'GameController@destroyByBggId')->where('bgg_game_id','[0-9]+');
    Route::post('games/bgg', 'GameController@storeFromLibrary');
    Route::get('games/search/name/{name}', 'GameController@searchGames');
    Route::get('games/search/letter/{letter}', 'GameController@searchLetter');

    //libraries
    Route::get('libraries', 'LibraryController@index');
    Route::post('libraries', 'LibraryController@store');
    Route::get('libraries/{library_id}', 'LibraryController@show')->where('library_id','[0-9]+');
    Route::put('libraries/{library_id}', 'LibraryController@update')->where('library_id','[0-9]+');
    Route::delete('libraries/{library_id}', 'LibraryController@destroy')->where('library_id','[0-9]+');
    Route::get('libraries/user/{username}', 'LibraryController@showByUser');
    Route::get('libraries/game/{bgg_game_id}', 'LibraryController@showByBggId')->where('bgg_game_id','[0-9]+');
    Route::delete('libraries/user/{username}/game/{bgg_game_id}', 'LibraryController@destroyByUserAndGame')->where('bgg_game_id','[0-9]+');

    //friends
    Route::get('friends', 'FriendController@index');
    Route::post('friends', 'FriendController@store');
    Route::get('friends/{friends_id}', 'FriendController@show')->where('friends_id','[0-9]+');
    Route::put('friends/{friends_id}', 'FriendController@update')->where('friends_id','[0-9]+');
    Route::delete('friends/{friends_id}', 'FriendController@destroy')->where('friends_id','[0-9]+');
    Route::get('friends/user/{username}', 'FriendController@showByUser');
    Route::get('friends/friend/{friend_username}', 'FriendController@showByFriend');
    Route::get('friends/are-friends/user/{username}/friend/{friend_username}', 'FriendController@areFriends');
    Route::delete('friends/user/{username}/friend/{friend_username}', 'FriendController@deleteByUserAndFriend');

    //play
    Route::get('play', 'PlayController@index');
    Route::post('play', 'PlayController@store');
    Route::get('play/{play_id}', 'PlayController@show')->where('play_id','[0-9]+');
    Route::put('play/{play_id}', 'PlayController@update')->where('play_id','[0-9]+');
    Route::delete('play/{play_id}', 'PlayController@destroy')->where('play_id','[0-9]+');
    Route::get('play/friends-not-in-play/{play_id}', 'PlayController@showFriendsNotInPlay')->where('play_id','[0-9]+');
    Route::get('play/user/{play_id}', 'PlayController@showByUser')->where('play_id','[0-9]+');

    //team
    Route::get('users', 'UserController@index');
    Route::post('team', 'TeamController@store');
    Route::get('team/{team_id}', 'TeamController@show')->where('team_id','[0-9]+');
    Route::put('team/{team_id}', 'TeamController@update')->where('team_id','[0-9]+');
    Route::delete('team/{team_id}', 'TeamController@destroy')->where('team_id','[0-9]+');
    Route::get('team/play/{play_id}', 'TeamController@showByPlay')->where('play_id','[0-9]+');

    //player
    Route::get('player', 'PlayerController@index');
    Route::post('player', 'PlayerController@store');
    Route::get('player/{player_id}', 'PlayerController@show')->where('player_id','[0-9]+');
    Route::put('player/{player_id}', 'PlayerController@update')->where('player_id','[0-9]+');
    Route::delete('player/{player_id}', 'PlayerController@destroy')->where('player_id','[0-9]+');
    Route::get('player/play/{play_id}', 'PlayerController@showByPlay')->where('play_id','[0-9]+');
    Route::get('player/team/{play_id}', 'PlayerController@showByTeam')->where('play_id','[0-9]+');

    //test
    //Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
    //Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
    Route::post('test/request', 'TestController@request');
});
