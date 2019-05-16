<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Exception;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function readAll()
    {
        return response(['success' => true, 'result' => Game::all()], 200);
    }

    public function read($game_id)
    {
        $game = Game::where('gameId', '=', $game_id)->first();
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this id'], 200);
        return response(['success' => true, 'result' => $game], 200);
    }

    public function delete($game_id)
    {
        $game = Game::where('gameId', '=', $game_id)->first();
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this id'], 200);
        try {
            $game->delete();
        } catch (Exception $e) {
        }
        return response(['success' => true, 'result' => $game->name], 200);
    }

    public function createFromLibrary(Request $request)
    {
        try
        {
            $bgg_username = $request->get("bgg_username") ? $request->get("bgg_username") : 'nemo';
            $url = "https://bgg-json.azurewebsites.net/collection/".$bgg_username;
            $json = file_get_contents($url);
            $list = json_decode($json, true);
            if ($list == null)
                return response(['success' => false, 'result' => 'This user does not exist on Board Game Geek']);
            $counter = 0;
            foreach ($list as $game)
            {
                $exist = Game::where('gameId', '=', $game['gameId'])->first();
                if ($exist != null) continue;
                ++$counter;
                Game::create([
                    'gameId' => $game['gameId'],
                    'name' => $game['name'],
                    'image' => $game['image'],
                    'thumbnail' => $game['thumbnail'],
                    'averageRating' => $game['averageRating'],
                    'rank' => $game['rank'],
                    'yearPublished' => $game['yearPublished'],
                    'minPlayers' => $game['minPlayers'],
                    'maxPlayers' => $game['maxPlayers'],
                    'playingTime' => $game['playingTime']
                ]);
            }
            return response()->json(['success' => false, 'result' => ['added' => $counter]]);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'result' => $e->getMessage()]);
        }
    }
}
