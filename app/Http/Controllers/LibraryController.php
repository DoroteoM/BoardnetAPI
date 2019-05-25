<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Models\Library;
use Exception;
use Illuminate\Http\Request;
use function PhpParser\filesInDir;
use Validator;

class LibraryController extends Controller
{

    public function create(Request $request)
    {
        $errors = $this->libraryDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $user = User::where('username', '=', $request->get("username"))->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $game = Game::where('gameId', '=', $request->get("bggGameId"))->first();
        if ($game == null)
            return response()->json(['success' => false, 'result' => "Game does not exist."]);
        $copy = Library::where('userId', '=', $user->id)->where('gameId', '=', $game->id)->first();
        if ($copy != null)
            return response()->json(['success' => false, 'result' => "Game is already in users library."]);

        $library = Library::create([
            'userId' => $user->id,
            'gameId' => $game->id,
            'dateAcquired' => $request->get("dateAcquired")
        ]);

        $library->game = $game->name;
        $library->username = $user->username;

        return response()->json(['success' => true, 'result' => $library]);
    }

    public function readByUser($username)
    {
        $user = User::where('username','=',$username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $list = Library::where('userId', '=', $user->id)->get();
        return response(['success' => true, 'result' => $list], 200);
    }

    protected function libraryDataValidator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'bggGameId' => 'required|integer',
            'dateAcquired' => 'date|date_format:Y-m-d|after:1900-01-01'
        ]);
    }
}
