<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function read ($username)
    {
        $user = User::where('username', $username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        return response()->json(['success' => true, 'result' => $user]);
    }

    public function update (Request $request, $user_id)
    {
        $errors = $this->userDataValidator($request->all(), $user_id)->errors();
        if(count($errors))
        {
            return response(['success' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $user = User::find($user_id);
        $user->username = $request->get("username");
        $user->email = $request->get("email");
        $user->name = $request->get("name", null);
        $user->surname = $request->get("surname", null);
        $user->date_of_birth = $request->get("date_of_birth", null);
        $user->bgg_username = $request->get("bgg_username", null);
        $user->save();
        return response(['success' => true, 'result' => $user], 200);
    }

    public function delete ($user_id)
    {
        $user = User::find($user_id);

        if ($user == null)
            return response(['success' => false, 'result' => "User does not exist"], 200);

        $libraries = $user->libraries;
        foreach ($libraries as $library) $library->delete();

        $friends_user = $user->friends_user;
        foreach ($friends_user as $friend) $friend->delete();

        $friends_friend = $user->friends_friend;
        foreach ($friends_friend as $friend) $friend->delete();

        $plays = $user->plays;
        foreach ($plays as $play) $play->delete();

        $players = $user->players;
        foreach ($players as $player)
        {
            $player->user_id = null;
            $player->save(); //TODO test this
        }

        $user->delete();

        return response(['success' => true, 'result' => ['deleted' => $user->username]], 200);
    }

    protected function userDataValidator(array $data, $user_id)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255|unique:users,username,'.$user_id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user_id,
            'date_of_birth' => 'date|date_format:Y-m-d|after:1900-01-01|before:today'
        ]);
    }
}
