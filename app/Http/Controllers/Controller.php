<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Mockery\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $loggedUser;
    protected $authMessage;
    public function authenticateRequest()
    {
        try
        {
            $this->loggedUser = JWTAuth::parseToken()->authenticate();
        }
        catch (\Exception $exception)
        {
            $this->authMessage = $exception->getMessage();
        }
    }

    public function authorizeRequest($user_id)
    {
        if ($this->loggedUser != null && $this->loggedUser->id != $user_id)
            $this->authMessage = 'You can not execute action with this user';
    }
}
