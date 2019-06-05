<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Play;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PlayController extends Controller
{
    public function create(Request $request)
    {
        $errors = $this->playDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $user = User::where('username', '=', $request->get("username"))->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $game = Game::where('bgg_game_id', '=', $request->get("bgg_game_id"))->first();
        if ($game == null)
            return response()->json(['success' => false, 'result' => "Game does not exist."]);

        $play = Play::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'mode' => $request->get("mode"),
            'duration' => $request->get("duration")
        ]);

        $play->user;
        $play->game;
        return response()->json(['success' => true, 'result' => $play]);
    }

    public function read($play_id)
    {
        $play = Play::find($play_id);

        $play->user;
        $play->game;
        return response()->json(['success' => true, 'result' => $play]);
    }

    public function readByUser($username)
    {
        $user = User::where('username', '=', $username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $plays = Play::where('user_id','=',$user->id)->get();

        foreach ($plays as $play)
        {
            $play->game;
        }
        return response()->json(['success' => true, 'result' => $plays]);
    }

    public function update(Request $request, $play_id)
    {
        $errors = $this->playUpdateDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $play = Play::find($play_id);

        $play->mode = $request->get('mode');
        $play->duration = $request->get('duration');
        $play->save();

        return response()->json(['success' => true, 'result' => $play]);
    }

    public function delete($play_id)
    {
        $play = Play::find($play_id);
        if ($play == null)
            return response(['success' => false, 'result' => 'Play with this id does not exist.'], 200);
        try {
            $play->delete();
        } catch (Exception $e) {
        }

        return response()->json(['success' => true, 'result' => $play]);
    }

    protected function playDataValidator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'bgg_game_id' => 'required|integer',
            'duration' => 'integer',
            'mode' => 'string|in:PVP,TEAM,COOP,MASTER,SOLO'
        ]);
    }

    private function playUpdateDataValidator(array $data)
    {
        return Validator::make($data, [
            'duration' => 'integer',
            'mode' => 'string|in:PVP,TEAM,COOP,MASTER,SOLO'
        ]);
    }
}
