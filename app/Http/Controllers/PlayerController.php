<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class PlayerController extends Controller
{
    public function create(Request $request)
    {
        $errors = $this->playerDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        if ($request->get('username') == null && $request->get('name') == null) {
            return response()->json(['success' => false, 'result' => "You must enter username or players name."]);
        }
        $play = Play::find($request->get('play_id'));
        if ($play == null)
            return response()->json(['success' => false, 'result' => "Play does not exist."]);
        $user = User::where('username', '=', $request->get('username'))->first();
        if ($request->get('username') && $user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $team = Team::find($request->get('team_id'));
        if ($request->get('team_id') && $team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);
        if ($team->play_id != $request->get('play_id'))
        {
            return response()->json(['success' => false, 'result' => "Team does not belong to this play exist."]);
        }
        if ($user != null) {
            $user_in_play = Player::where('play_id','=',$play->id)->where('user_id','=',$user->id)->get();
            if ($user_in_play->isNotEmpty())
                return response()->json(['success' => false, 'result' => "User is already in this play."]);
        }

        $player = Player::create([
            'play_id' => $play->id,
            'user_id' => $user ? $user->id : null,
            'name' => $request->get('name') ? $request->get('name') : $user->name,
            'team_id' => $team ? $team->id : null,
            'won' => $request->get("won"),
            'points' => $request->get("points")
        ]);

        $player->user;
        $player->play->game;
        $player->team;
        return response()->json(['success' => true, 'result' => $player]);
    }

    protected function playerDataValidator(array $data)
    {
        return Validator::make($data, [
            'play_id' => 'required|integer',
            'username' => 'string',
            'name' => 'string',
            'team_id' => 'integer',
            'won' => 'boolean',
            'points' => 'integer'
        ]);
    }
}
