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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->get('/hello', function () {
    return json_encode(['message' => 'hello world']);
});

Route::middleware('api')->get('/hello2/{hello}', function ($hello) {
    return json_encode(['message' => $hello]);
});

Route::group(['middleware' => ['api','cors']], function () {

    //registracija i login
    Route::post('auth/register', 'Auth\ApiRegisterController@register');
    Route::post('auth/login', 'Auth\ApiAuthController@login');

    //user
    Route::get('user/details/{username}', 'UserController@show_details');
    Route::put('user/details/{user_id}', 'UserController@save_details');

    //game
    Route::get('games/library/addgames/user/{username}', 'GameController@add_games_from_library');

    //test
    //Route::post('auth/gettoken', 'Auth\ApiAuthController@authenticate');//I can get token!
    //Route::post('auth/getuser', 'Auth\ApiAuthController@getUser');// I can't get user
    Route::get('test/library', 'TestController@test_library');
    Route::get('test/hello', 'TestController@hello');
    Route::post('test/hello1', 'TestController@hello1');
    Route::post('test/hello2', 'TestController@hello2');
});
