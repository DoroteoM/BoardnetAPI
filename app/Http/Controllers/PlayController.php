<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Play;
use App\Models\Player;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;

class PlayController extends Controller
{
    public function index()
    {
        $plays = Play::paginate(50);
        return response(['success' => true, 'result' => $plays], 200);
    }

    public function store(Request $request)
    {
        $errors = $this->playDataValidator($request->all())->errors();
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

    public function show($play_id)
    {
        $this->authenticateRequest();
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $play = Play::find($play_id);
        if ($play == null)
            return response()->json(['success' => false, 'result' => "Play does not exist."]);

        $play->user;
        $play->game;

        if ($play->teams->isNotEmpty())
        {
            foreach ($play->teams as $team) $team->players;
        }
        else
        {
            $players = Player::where('play_id', $play->id)->orderBy('won','desc')->orderBy('points','desc')->get();
            foreach ($players as $player) $player->user;
            $play->players = $players;
        }

        return response()->json(['success' => true, 'result' => $play]);
    }

    public function update(Request $request, $play_id)
    {
        $errors = $this->playUpdateDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['success' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $play = Play::find($play_id);

        $this->authenticateRequest();
        $this->authorizeRequest($play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $play->mode = $request->get('mode');
        $play->duration = $request->get('duration');
        $play->save();

        return response()->json(['success' => true, 'result' => $play]);
    }

    public function destroy($play_id)
    {
        $play = Play::find($play_id);
        if ($play == null)
            return response(['success' => false, 'result' => 'Play with this id does not exist.'], 200);

        $this->authenticateRequest();
        $this->authorizeRequest($play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        try {
            $play->delete();
        } catch (Exception $e) {
        }

        return response()->json(['success' => true, 'result' => $play]);
    }

    public function showFriendsNotInPlay($play_id)
    {
        $play = Play::find($play_id);
        if ($play == null)
            return response()->json(['success' => false, 'result' => "Play does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $me = User::find($play->user_id);

        $friends = collect([$me]);
        foreach ($play->user->friends_friend as $friend)
        {
            $friends->push($friend->friend);
        }

        $players = collect([]);
        foreach ($play->players as $player)
        {
            $players->push($player->user);
        }

        $friendsNotInPlay = collect([]);
        foreach ($friends as $friend)
        {
            if (!$players->contains($friend) && $friend != null)
                $friendsNotInPlay->push($friend);
        }

        return response()->json(['success' => true, 'result' => $friendsNotInPlay]);
    }

    public function showByUser($username)
    {
        $user = User::where('username', '=', $username)->first();
        if ($user == null)
            return response()->json(['success' => false, 'result' => "User does not exist."]);
        $plays = Play::where('user_id','=',$user->id)->orderBy('id', 'desc')->get();

        foreach ($plays as $play)
        {
            $play->game;
        }
        return response()->json(['success' => true, 'result' => $plays]);
    }

    protected function playDataValidator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string',
            'bgg_game_id' => 'required|integer',
            'duration' => 'integer',
            'mode' => 'string|in:SOLO,PVP,TEAM,COOP,MASTER'
        ]);
    }

    private function playUpdateDataValidator(array $data)
    {
        return Validator::make($data, [
            'duration' => 'integer',
            'mode' => 'string|in:SOLO,PVP,TEAM,COOP,MASTER'
        ]);
    }
}
