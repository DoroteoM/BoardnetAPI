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

    public function update (Request $request, $userId)
    {
        $errors = $this->userDataValidator($request->all(), $userId)->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $user = User::find($userId);
        $user->username = $request->get("username");
        $user->email = $request->get("email");
        $user->name = $request->get("name", null);
        $user->surname = $request->get("surname", null);
        $user->dateOfBirth = $request->get("dateOfBirth", null);
        $user->bggUsername = $request->get("bggUsername", null);
        $user->save();
        return response(['success' => true, 'result' => $user], 200);
    }

    public function delete ($userId)
    {
        $user = User::find($userId);

        if ($user == null)
            return response(['success' => false, 'result' => "User does not exist"], 200);

        $user->delete();

        return response(['success' => true, 'result' => ['deleted' => $user->username]], 200);
    }

    protected function userDataValidator(array $data, $userId)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255|unique:users,username,'.$userId,
            'email' => 'required|string|email|max:255|unique:users,email,'.$userId,
            'dateOfBirth' => 'date|date_format:Y-m-d|after:1900-01-01|before:today'
        ]);
    }
}
