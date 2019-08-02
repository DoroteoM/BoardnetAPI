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
    public function index()
    {
        $library = Library::paginate(50);
        return response(['success' => true, 'result' => $library], 200);
    }

    public function store(Request $request)
    {
        $errors = $this->libraryDataValidator($request->all())->errors();
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

        $game = Game::where('bgg_game_id', '=', $request->get("bgg_game_id"))->first();
        if ($game == null)
            return response()->json(['success' => false, 'result' => "Game does not exist."]);
        $copy = Library::where('user_id', '=', $user->id)->where('game_id', '=', $game->id)->first();
        if ($copy != null)
            return response()->json(['success' => false, 'result' => "Game is already in users library."]);

        $library = Library::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'date_acquired' => $request->get("date_acquired")
        ]);

        $library->game = $game->name;
        $library->username = $user->username;

        return response()->json(['success' => true, 'result' => $library]);
    }

    public function show ($id) {
        $this->authenticateRequest();
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $library = Library::find($id);
        if ($library == null)
            return response()->json(['success' => false, 'result' => $library], 404);
        return response()->json(['success' => true, 'result' => $library]);
    }

    public function update (Request $request, $library_id)
    {
        $errors = $this->dateDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['success' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $library = Library::find($library_id);
        if ($library == null)
            return response()->json(['success' => false, 'result' => "Library with this id does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($library->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $library->date_acquired = $request->get("date_acquired");
        $library->save();

        return response()->json(['success' => true, 'result' => $library]);
    }

    public function destroy($library_id)
    {
        $library = Library::find($library_id);
        if ($library == null)
            return response(['success' => false, 'result' => 'Library with this id does not exist.'], 200);

        $this->authenticateRequest();
        $this->authorizeRequest($library->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        try {
            $library->delete();
        } catch (Exception $e) {
        }
        return response(['success' => true, 'result' => $library], 200);
    }

    public function showByUser($username)
    {
        $user = User::where('username','=',$username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $games = Game::whereHas('libraries', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        })->orderBy('name','asc')->get();
        if ($games->isEmpty())
            $list[] = null;
        foreach($games as $game)
        {
            $item = new Game;
            $item->id = $game->id;
            $item->bgg_game_id = $game->bgg_game_id;
            $item->name = $game->name;
            $item->thumbnail = $game->thumbnail;
            $list[] = $item->toArray();
        }
        return response(['success' => true, 'result' => $list], 200);
    }

    public function showByBggId($bgg_game_id)
    {
        $game = Game::where('bgg_game_id','=',$bgg_game_id)->first();
        if ($game == null)
            return response()->json(['success' => false, 'result' => "Game does not exist."]);

        $list = User::whereHas('libraries', function ($query) use ($game) {
            $query->where('game_id', '=', $game->id);
        })->get();

        return response(['success' => true, 'result' => $list], 200);
    }

    public function destroyByUserAndGame($username, $bgg_game_id)
    {
        $user = User::where('username', '=', $username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $game = Game::where('bgg_game_id', '=', $bgg_game_id)->first();
        if ($game == null)
            return response()->json(['success' => false, 'result' => "Game does not exist."]);

        $library = Library::where('user_id','=', $user->id)->where('game_id','=', $game->id)->first();
        if ($library == null)
            return response(['success' => false, 'result' => 'This game is not in users library.'], 200);
        try {
            $library->delete();
        } catch (Exception $e) {
        }
        return response(['success' => true, 'result' => $library], 200);
    }

    protected function libraryDataValidator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'bgg_game_id' => 'required|integer',
            'date_acquired' => 'date|date_format:Y-m-d|after:1900-01-01'
        ]);
    }

    protected function dateDataValidator(array $data)
    {
        return Validator::make($data, [
            'date_acquired' => 'date|date_format:Y-m-d|after:1900-01-01'
        ]);
    }
}
