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
    Route::post('users/update/{user_id}', 'UserController@update');
    Route::get('users/delete/{user_id}', 'UserController@delete');

    //games
    Route::post('games/bgg', 'GameController@createFromLibrary');
    Route::get('games', 'GameController@readAll');
    Route::get('games/{bgg_game_id}', 'GameController@read');
    Route::post('games/update/{bgg_game_id}', 'GameController@update');
    Route::get('games/delete/{bgg_game_id}', 'GameController@delete');

    //libraries
    Route::post('libraries', 'LibraryController@create');
    Route::get('libraries/user/{username}', 'LibraryController@readByUser');
    Route::get('libraries/game/{bgg_game_id}', 'LibraryController@readByGame');
    Route::post('libraries/update/{library_id}', 'LibraryController@update');
    Route::get('libraries/delete/{library_id}', 'LibraryController@delete');
    Route::get('libraries/delete/user/{username}/game/{bgg_game_id}', 'LibraryController@deleteByUserAndGame');

    //friends
    Route::post('friends', 'FriendController@create');
    Route::get('friends/user/{username}', 'FriendController@readByUser');
    Route::get('friends/friend/{friend_username}', 'FriendController@readByFriend');
    Route::post('friends/update/{friends_id}', 'FriendController@update');
    Route::get('friends/delete/{friends_id}', 'FriendController@delete');
    Route::get('friends/delete/user/{username}/friend/{friend_username}', 'FriendController@deleteByUserAndFriend');

    //play
    Route::post('play', 'PlayController@create');
    Route::get('play/{play_id}', 'PlayController@read');
    Route::get('play/user/{play_id}', 'PlayController@readByUser');
    Route::post('play/update/{play_id}', 'PlayController@update');
    Route::get('play/delete/{play_id}', 'PlayController@delete');

    //team
    Route::post('team', 'TeamController@create');
    Route::get('team/{team_id}', 'TeamController@read');
    Route::get('team/play/{play_id}', 'TeamController@readByPlay');
    Route::post('team/update/{team_id}', 'TeamController@update');
    Route::get('team/delete/{team_id}', 'TeamController@delete');

    //player
    Route::post('player', 'PlayerController@create');
    Route::get('player/{player_id}', 'PlayerController@read');
    Route::get('player/play/{play_id}', 'PlayerController@readByPlay');
    Route::get('player/team/{play_id}', 'PlayerController@readByTeam');
    Route::post('player/update/{player_id}', 'PlayerController@update');
    Route::get('player/delete/{player_id}', 'PlayerController@delete');

    //test
    //Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
    //Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
});
