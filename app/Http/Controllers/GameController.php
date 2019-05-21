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

    public function read($gameId)
    {
        $game = Game::where('gameId', '=', $gameId)->first();
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this id'], 200);
        return response(['success' => true, 'result' => $game], 200);
    }

    public function update($gameId)
    {
        try
        {
            $url = "https://bgg-json.azurewebsites.net/thing/".$gameId;
            $json = file_get_contents($url);
            $bggGame = json_decode($json, true);
            if ($bggGame == null)
                return response(['success' => false, 'result' => 'This game does not exist on Board Game Geek']);
            $game = Game::where('gameId', '=', $gameId)->first();
            $game->name = $bggGame['name'];
            $game->image = $bggGame['image'];
            $game->thumbnail = $bggGame['thumbnail'];
            $game->averageRating = $bggGame['averageRating'];
            $game->rank = $bggGame['rank'];
            $game->yearPublished = $bggGame['yearPublished'];
            $game->minPlayers = $bggGame['minPlayers'];
            $game->maxPlayers = $bggGame['maxPlayers'];
            $game->playingTime = $bggGame['playingTime'];

            $game->save();
            return response(['success' => true, 'result' => $game], 200);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'result' => $e->getMessage()]);
        }
    }


    public function delete($gameId)
    {
        $game = Game::where('gameId', '=', $gameId)->first();
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
            $bggUsername = $request->get("bggUsername") ? $request->get("bggUsername") : 'nemo';
            $url = "https://bgg-json.azurewebsites.net/collection/".$bggUsername;
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
