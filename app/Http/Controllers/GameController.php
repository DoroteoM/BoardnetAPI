<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    public function add_games_from_library($username)
    {
        try
        {
            $url = "https://bgg-json.azurewebsites.net/collection/".$username;
            $json = file_get_contents($url);
            $list = json_decode($json, true);
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
            //return response()->json(Game::all());
            if ($counter == 0) return response()->json(['Result' => 'Error', 'Message' => 'All games are already on the list']);
            return response()->json(['response' => 'success', 'result' => $counter]);
        }
        catch (\Exception $e)
        {
            return response()->json(['response' => 'error', 'result' => $e->getMessage()]);
        }
    }
}
