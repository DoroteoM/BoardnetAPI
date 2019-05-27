<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Library;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class FriendController extends Controller
{
    public function create(Request $request)
    {
        $errors = $this->friendDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $user = User::where('username', '=', $request->get("username"))->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $friend = User::where('username', '=', $request->get("friend_username"))->first();
        if ($friend == null)
            return response()->json(['success' => false, 'result' => "Friend with this username does not exist."]);
        $copy = Friend::where('user_id', '=', $user->id)->where('friend_id', '=', $friend->id)->first();
        if ($user == $friend)
            return response()->json(['success' => false, 'result' => "User can not add himself."]);
        if ($copy != null)
            return response()->json(['success' => false, 'result' => "Friend is already added."]);

        $friendship = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id
        ]);

        $friendship->user = $user->username;
        $friendship->friend = $friend->username;

        return response()->json(['success' => true, 'result' => $friendship]);
    }

    private function friendDataValidator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'friend_username' => 'required|string',
        ]);
    }
}
