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

    public function read($bgg_game_id)
    {
        $game = Game::where('bgg_game_id', '=', $bgg_game_id)->first();
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this id'], 200);
        return response(['success' => true, 'result' => $game], 200);
    }

    public function update($bgg_game_id)
    {
        try
        {
            $url = "https://bgg-json.azurewebsites.net/thing/".$bgg_game_id;
            $json = file_get_contents($url);
            $bgg_game = json_decode($json, true);
            if ($bgg_game == null)
                return response(['success' => false, 'result' => 'This game does not exist on Board Game Geek']);
            $game = Game::where('bgg_game_id', '=', $bgg_game_id)->first();
            if ($game == null)
            {
                $game = Game::create([
                    'bgg_game_id' => $bgg_game['gameId'],
                    'name' => $bgg_game['name'],
                    'image' => $bgg_game['image'],
                    'thumbnail' => $bgg_game['thumbnail'],
                    'average_rating' => $bgg_game['averageRating'],
                    'rank' => $bgg_game['rank'],
                    'year_published' => $bgg_game['yearPublished'],
                    'min_players' => $bgg_game['minPlayers'],
                    'max_players' => $bgg_game['maxPlayers'],
                    'playing_time' => $bgg_game['playingTime']
                ]);
            }
            else
            {
                $game->name = $bgg_game['name'];
                $game->image = $bgg_game['image'];
                $game->thumbnail = $bgg_game['thumbnail'];
                $game->average_rating = $bgg_game['averageRating'];
                $game->rank = $bgg_game['rank'];
                $game->year_published = $bgg_game['yearPublished'];
                $game->min_players = $bgg_game['minPlayers'];
                $game->max_players = $bgg_game['maxPlayers'];
                $game->playing_time = $bgg_game['playingTime'];
                $game->save();
            }

            return response(['success' => true, 'result' => $game], 200);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'result' => $e->getMessage()]);
        }
    }


    public function delete($bgg_game_id)
    {
        $game = Game::where('bgg_game_id', '=', $bgg_game_id)->first();
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
            foreach ($list as $bgg_game)
            {
                $exist = Game::where('bgg_game_id', '=', $bgg_game['gameId'])->first();
                if ($exist != null) continue;
                ++$counter;
                Game::create([
                    'bgg_game_id' => $bgg_game['gameId'],
                    'name' => $bgg_game['name'],
                    'image' => $bgg_game['image'],
                    'thumbnail' => $bgg_game['thumbnail'],
                    'average_rating' => $bgg_game['averageRating'],
                    'rank' => $bgg_game['rank'],
                    'year_published' => $bgg_game['yearPublished'],
                    'min_players' => $bgg_game['minPlayers'],
                    'max_players' => $bgg_game['maxPlayers'],
                    'playing_time' => $bgg_game['playingTime']
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
