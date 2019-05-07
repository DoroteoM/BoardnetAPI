<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class ApiRegisterController extends RegisterController
{
    /**
     * Handle a registration request for the application.
     *
     * @override
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $errors = $this->validator($request->all())->errors();

        if(count($errors))
        {
            return response(['success' => false, 'errors' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return response(['success' => true, 'user' => $user]);
    }
}
