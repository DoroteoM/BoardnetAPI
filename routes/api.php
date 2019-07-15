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
    Route::get('users/search/name/{name}', 'UserController@searchByName');
    Route::get('users/search/username/{username}', 'UserController@searchByUsername');

    //games
    Route::post('games/bgg', 'GameController@createFromLibrary');
    Route::get('games', 'GameController@readAll');
    Route::get('games/{bgg_game_id}/{username?}', 'GameController@read');
    Route::put('games/{bgg_game_id}', 'GameController@update');
    Route::delete('games/{bgg_game_id}', 'GameController@delete');
    Route::get('games/search/name/{name}', 'GameController@searchGames');
    Route::get('games/search/letter/{letter}', 'GameController@letter');

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
    Route::get('friends/are-friends/user/{username}/friend/{friend_username}', 'FriendController@areFriends');
    Route::put('friends/{friends_id}', 'FriendController@update');
    Route::delete('friends/{friends_id}', 'FriendController@delete');
    Route::delete('friends/user/{username}/friend/{friend_username}', 'FriendController@deleteByUserAndFriend');

    //play
    Route::post('play', 'PlayController@create');
    Route::get('play/{play_id}', 'PlayController@read');
    Route::get('play/friends-not-in-play/{play_id}', 'PlayController@readFriendsNotInPlay');
    Route::get('play/user/{play_id}', 'PlayController@readByUser');
    Route::put('play/{play_id}', 'PlayController@update');
    Route::delete('play/{play_id}', 'PlayController@delete');

    //team
    Route::post('team', 'TeamController@create');
    Route::get('team/{team_id}', 'TeamController@read');
    Route::get('team/play/{play_id}', 'TeamController@readByPlay');
    Route::put('team/{team_id}', 'TeamController@update');
    Route::delete('team/{team_id}', 'TeamController@delete');

    //player
    Route::post('player', 'PlayerController@create');
    Route::get('player/{player_id}', 'PlayerController@read');
    Route::get('player/play/{play_id}', 'PlayerController@readByPlay');
    Route::get('player/team/{play_id}', 'PlayerController@readByTeam');
    Route::put('player/{player_id}', 'PlayerController@update');
    Route::delete('player/{player_id}', 'PlayerController@delete');

    //test
    //Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
    //Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
});
