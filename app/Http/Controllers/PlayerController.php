<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::paginate(50);
        return response(['success' => true, 'result' => $players], 200);
    }

    public function store(Request $request)
    {
        $errors = $this->playerDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['success' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $play = Play::find($request->get('play_id'));
        if ($play == null)
            return response()->json(['success' => false, 'result' => "Play does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        if ($request->get('username') == null && $request->get('name') == null)
            return response()->json(['success' => false, 'result' => "You must enter username or players name."]);
        $user = User::where('username', '=', $request->get('username'))->first();
        if ($request->get('username') && $user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $team = Team::find($request->get('team_id'));
        if ($request->get('team_id') && $team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);
        if ($team != null) {
            if ($team->play_id != $request->get('play_id'))
                return response()->json(['success' => false, 'result' => "Team does not belong to this play."]);
        }
        if ($user != null) {
            $user_in_play = Player::where('play_id','=',$play->id)->where('user_id','=',$user->id)->get();
            if ($user_in_play->isNotEmpty())
                return response()->json(['success' => false, 'result' => "User is already in this play."]);
        }

        if ($user != null)
            $name = $request->get('name') ? $request->get('name') : $user->name ? $user->name : $user->username;
        else
            $name = $request->get('name');

        $player = Player::create([
            'play_id' => $play->id,
            'user_id' => $user ? $user->id : null,
            'name' => $name,
            'team_id' => $team ? $team->id : null,
            'won' => $request->get("won"),
            'points' => $request->get("points")
        ]);

        $player->user;
        $player->play->game;
        $player->team;
        return response()->json(['success' => true, 'result' => $player]);
    }

    public function show($player_id)
    {
        $this->authenticateRequest();
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $player = Player::find($player_id);
        if ($player == null)
            return response()->json(['success' => false, 'result' => "Player does not exist."]);

        $player->user;
        $player->play->game;
        $player->team;
        return response()->json(['success' => true, 'result' => $player]);
    }

    public function update(Request $request, $player_id)
    {
        $errors = $this->playerUpdateDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['success' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $player = Player::find($player_id);
        if ($player == null)
            return response()->json(['success' => false, 'result' => "Player does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($player->play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        if ($request->get('username') == null && $request->get('name') == null)
            return response()->json(['success' => false, 'result' => "You must enter username or players name."]);
        $user = User::where('username', '=', $request->get('username'))->first();
        if ($request->get('username') && $user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $team = Team::find($request->get('team_id'));
        if ($request->get('team_id') && $team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);
        if ($user != null && $player->user_id != null && $player->user_id != $user->id) {
            $user_in_play = Player::where('play_id','=',$player->play_id)->where('user_id','=',$user->id)->get();
            if ($user_in_play->isNotEmpty())
                return response()->json(['success' => false, 'result' => "User is already in this play."]);
        }

        if ($user != null)
            $player->name = $request->get('name') ? $request->get('name') : $user->name ? $user->name : $user->username;
        else if ($request->get('name') != null)
            $player->name = $request->get('name');
        else
            return response()->json(['success' => false, 'result' => 'You must enter username or players name.']);
        $player->user_id = $user ? $user->id : null;
        $player->team_id = $team ? $team->id : null;
        $player->won = $request->get("won");
        $player->points = $request->get("points");
        $player->save();

        $player->user;
        $player->play->game;
        $player->team;
        return response()->json(['success' => true, 'result' => $player]);
    }

    public function destroy($player_id)
    {
        $player = Player::find($player_id);
        if ($player == null)
            return response(['success' => false, 'result' => 'Player with this id does not exist.'], 200);

        $this->authenticateRequest();
        $this->authorizeRequest($player->play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        try {
            $player->delete();
        } catch (Exception $e) {
        }

        return response()->json(['success' => true, 'result' => $player]);
    }

    public function showByPlay($play_id)
    {
        $play = Play::find($play_id);
        if ($play == null)
            return response()->json(['success' => false, 'result' => "Play does not exist."]);

        $players = $play->players;
        foreach ($players as $player)
        {
            $player->user;
            $player->team;
        }
        return response()->json(['success' => true, 'result' => $players]);
    }

    public function showByTeam($team_id)
    {
        $team = Team::find($team_id);
        if ($team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);

        $players = $team->players;
        foreach ($players as $player)
        {
            $player->user;
        }
        return response()->json(['success' => true, 'result' => $players]);
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

    private function playerUpdateDataValidator(array $data)
    {
        return Validator::make($data, [
            'username' => 'string',
            'name' => 'string',
            'team_id' => 'integer',
            'won' => 'boolean',
            'points' => 'integer'
        ]);
    }
}
