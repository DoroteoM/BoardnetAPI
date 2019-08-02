<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class FriendController extends Controller
{
    public function index()
    {
        $friends = Friend::paginate(50);
        return response(['success' => true, 'result' => $friends], 200);
    }

    public function store(Request $request)
    {
        $errors = $this->friendDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['success' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $user = User::where('username', '=', $request->get("username"))->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

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

    public function show ($id) {
        $friendship = Friend::find($id);
        if ($friendship == null)
            return response()->json(['success' => false, 'result' => 'There is no friendship with this id'], 404);

        $this->authenticateRequest();
        $this->authorizeRequest($friendship->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        return response()->json(['success' => true, 'result' => $friendship]);
    }

    public function update($id)
    {
        $friendship = Friend::find($id);
        if ($friendship == null)
            return response()->json(['success' => false, 'result' => 'There is no friendship with this id'], 404);

        $this->authenticateRequest();
        $this->authorizeRequest($friendship->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        return response()->json(['success' => false, 'result' => 'I\'m not doing anything']);
    }

    public function destroy($id)
    {
        $friendship = Friend::find($id);
        if ($friendship == null)
            return response(['success' => false, 'result' => 'There is no friendship with this id'], 200);

        $this->authenticateRequest();
        $this->authorizeRequest($friendship->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        try {
            $friendship->delete();
        } catch (Exception $e) {
        }
        return response(['success' => true, 'result' => $friendship], 200);
    }

    public function showByUser($username)
    {
        $user = User::where('username', '=', $username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);

        $friends = Friend::where('user_id','=',$user->id)->get();

        foreach ($friends as $friend)
        {
            $friend->user;
            $friend->friend;
        }

        return response()->json(['success' => true, 'result' => $friends]);
    }

    public function showByFriend($friend_username)
    {
        $friend = User::where('username', '=', $friend_username)->first();
        if ($friend == null)
            return response()->json(['success' => false, 'result' => "Friend does not exist."]);

        $friends = Friend::where('friend_id','=',$friend->id)->get();

        foreach ($friends as $friend)
        {
            $friend->user;
            $friend->friend;
        }

        return response()->json(['success' => true, 'result' => $friends]);
    }

    public function areFriends($username, $friend_username)
    {
        $user = User::where('username', '=', $username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $friend = User::where('username', '=', $friend_username)->first();
        if ($friend == null)
            return response()->json(['success' => false, 'result' => "Friend does not exist."]);

        $friends = Friend::where('user_id',$user->id)->where('friend_id',$friend->id)->get();

        return response()->json(['success' => true, 'result' => $friends->isNotEmpty()]);
    }

    public function destroyByUserAndFriend($username, $friend_username)
    {
        $user = User::where('username', '=', $username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $friend = User::where('username', '=', $friend_username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "Friend does not exist."]);

        $friendship = Friend::where('user_id','=', $user->id)->where('friend_id','=', $friend->id)->first();
        if ($friendship == null)
            return response(['success' => false, 'result' => 'This friend is not added by user.'], 200);
        try {
            $friendship->delete();
        } catch (Exception $e) {
        }
        return response(['success' => true, 'result' => $friendship], 200);
    }

    private function friendDataValidator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'friend_username' => 'required|string',
        ]);
    }
}
