<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Play;
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

        $game = Game::where('bgg_game_id', '=', $request->get("bgg_game_id"))->first();
        if ($game == null)
            return response()->json(['success' => false, 'result' => "Game does not exist."]);

        $play = Play::create([
            'game_id' => $game->id,
            'mode' => $request->get("mode"),
            'number_of_players' => $request->get("number_of_players"),
            'duration' => $request->get("duration")
        ]);

        $play->game = $game->name;

        return response()->json(['success' => true, 'result' => $play]);
    }

    protected function playDataValidator(array $data)
    {
        return Validator::make($data, [
            'bgg_game_id' => 'required|integer',
            'mode' => 'string',
            'number_of_players' => 'integer',
            'duration' => 'integer'
        ]);
    }
}
