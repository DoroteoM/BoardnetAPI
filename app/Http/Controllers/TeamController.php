<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Validator;

class TeamController extends Controller
{
    public function create(Request $request)
    {
        $errors = $this->teamDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $play = Play::find($request->get('play_id'));
        if ($play == null)
            return response()->json(['success' => false, 'result' => "Play does not exist."]);

        $team = Team::create([
            'play_id' => $request->get('play_id'),
            'name' => $request->get('name'),
            'won' => $request->get("won"),
            'points' => $request->get("points")
        ]);

        $team->play->game;
        return response()->json(['success' => true, 'result' => $team]);
    }

    public function read($team_id)
    {
        $team = Team::find($team_id);
        if ($team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);

        $team->play->game;
        return response()->json(['success' => true, 'result' => $team]);
    }

    public function readByPlay($play_id)
    {
        $team = Team::where('play_id', '=', $play_id)->get();
        return response()->json(['success' => true, 'result' => $team]);
    }

    public function update(Request $request, $team_id)
    {
        $errors = $this->teamUpdateDataValidator($request->all())->errors();
        if(count($errors))
        {
            return response(['response' => false, 'result' => $errors], 200); //! Na 401 aplkacija ne cita uspjesno odgovor
        }

        $team = Team::find($team_id);
        if ($team == null)
            return response()->json(['success' => false, 'result' => "Team does not exist."]);

        $team->name = $request->get('name');
        $team->won = $request->get("won");
        $team->points = $request->get("points");
        $team->save();

        $team->play->game;
        return response()->json(['success' => true, 'result' => $team]);
    }

    public function delete($team_id)
    {
        $team = Team::find($team_id);
        if ($team == null)
            return response(['success' => false, 'result' => 'Team with this id does not exist.'], 200);
        try {
            $team->delete();
        } catch (Exception $e) {
        }

        return response()->json(['success' => true, 'result' => $team]);
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
