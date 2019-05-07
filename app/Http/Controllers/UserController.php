<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show_details ($username)
    {
        $details = User::where('username', $username)->first();
        return response()->json(['success' => true, 'result' => $details]);
    }

    public function save_details (Request $request, $user_id)
    {
        $errors = $this->validator($request->all(), $user_id)->errors();

        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
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

    protected function validator(array $data, $user_id)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255|unique:users,username,'.$user_id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user_id,
            'date_of_birth' => 'date|date_format:Y-m-d|after:1900-01-01|before:today'
        ]);
    }
}
