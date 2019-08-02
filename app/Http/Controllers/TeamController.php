<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Validator;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::paginate(50);
        return response(['success' => true, 'result' => $teams], 200);
    }

    public function store(Request $request)
    {
        $errors = $this->teamDataValidator($request->all())->errors();
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

        $team = Team::create([
            'play_id' => $request->get('play_id'),
            'name' => $request->get('name'),
            'won' => $request->get("won"),
            'points' => $request->get("points")
        ]);

        $team->play->game;
        return response()->json(['success' => true, 'result' => $team]);
    }

    public function show($team_id)
    {
        $team = Team::find($team_id);
        if ($team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);

        $this->authenticateRequest();
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $team->play->game;
        $team->players;
        return response()->json(['success' => true, 'result' => $team]);
    }

    public function update(Request $request, $team_id)
    {
        $errors = $this->teamUpdateDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['success' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $team = Team::find($team_id);
        if ($team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);

        $this->authenticateRequest();
        $this->authorizeRequest($team->play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $team->name = $request->get('name');
        $team->won = $request->get("won");
        $team->points = $request->get("points");
        $team->save();

        $team->play->game;
        return response()->json(['success' => true, 'result' => $team]);
    }

    public function destroy($team_id)
    {
        $team = Team::find($team_id);
        if ($team == null)
            return response(['success' => false, 'result' => 'Team with this id does not exist.'], 200);

        $this->authenticateRequest();
        $this->authorizeRequest($team->play->user->id);
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        $players = $team->players;
        foreach ($players as $player) $player->delete();

        try {
            $team->delete();
        } catch (Exception $e) {
        }

        return response()->json(['success' => true, 'result' => $team]);
    }

    public function showByPlay($play_id)
    {
        $play = Play::find($play_id);
        if ($play == null)
            return response()->json(['success' => false, 'result' => "Play does not exist."]);

        $this->authenticateRequest();
        if ($this->authMessage != null)
            return response()->json(['success' => false, 'result' => $this->authMessage]);

        foreach ($play->teams as $team) $team->players;
        return response()->json(['success' => true, 'result' => $play->teams]);
    }

    protected function teamDataValidator(array $data)
    {
        return Validator::make($data, [
            'play_id' => 'required|integer',
            'name' => 'string',
            'won' => 'boolean',
            'points' => 'integer'
        ]);
    }

    protected function teamUpdateDataValidator(array $data)
    {
        return Validator::make($data, [
            'name' => 'string',
            'won' => 'boolean',
            'points' => 'integer'
        ]);
    }
}
