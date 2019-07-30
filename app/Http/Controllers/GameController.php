<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Library;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Object_;
use stdClass;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::paginate(50);
        return response(['success' => true, 'result' => $games], 200);
    }

    public function show($id, $username = null)
    {
        $game = Game::find($id);
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this id'], 200);
        if ($username != null)
        {
            $user = User::where('username','=',$username)->first();
            $inLibrary = Library::where('user_id','=',$user->id)
                ->where('game_id','=',$game->id)->first();
            if ($inLibrary != null)
                $game->inLibrary = true;
            else
                $game->inLibrary = false;
        }
        return response(['success' => true, 'result' => $game], 200);
    }

    public function update($id)
    {
        try
        {
            $game = Game::find($id);
            if ($game == null)
                return response(['success' => false, 'result' => 'There is no game with this id']);
            $bgg_game_id = $game->bgg_game_id;
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

    public function destroy($id)
    {
        $game = Game::find($id);
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this id'], 200);

        $libraries = $game->libraries;
        foreach ($libraries as $library) $library->delete();

        $plays = $game->plays;
        foreach ($plays as $play)
        {
            $teams = $play->teams;
            foreach ($teams as $team) $team->delete();
            $players = $play->players;
            foreach ($players as $player) $player->delete();
            $play->delete();
        }

        try {
            $game->delete();
        } catch (Exception $e) {
        }
        return response(['success' => true, 'result' => $game->name], 200);
    }

    public function showByBggId($bgg_game_id, $username = null)
    {
        $game = Game::where('bgg_game_id', '=', $bgg_game_id)->first();
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this bgg_id'], 200);
        if ($username != null)
        {
            $user = User::where('username','=',$username)->first();
            $inLibrary = Library::where('user_id','=',$user->id)
                ->where('game_id','=',$game->id)->first();
            if ($inLibrary != null)
                $game->inLibrary = true;
            else
                $game->inLibrary = false;
        }
        return response(['success' => true, 'result' => $game], 200);
    }

    public function updateByBggId($bgg_game_id)
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

    public function destroyByBggId($bgg_game_id)
    {
        $game = Game::where('bgg_game_id', '=', $bgg_game_id)->first();
        if ($game == null)
            return response(['success' => false, 'result' => 'There is no game with this id'], 200);

        $libraries = $game->libraries;
        foreach ($libraries as $library) $library->delete();

        $plays = $game->plays;
        foreach ($plays as $play)
        {
            $teams = $play->teams;
            foreach ($teams as $team) $team->delete();
            $players = $play->players;
            foreach ($players as $player) $player->delete();
            $play->delete();
        }

        try {
            $game->delete();
        } catch (Exception $e) {
        }
        return response(['success' => true, 'result' => $game->name], 200);
    }

    public function storeFromLibrary(Request $request)
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
            return response()->json(['success' => true, 'result' => ['added' => $counter]]);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'result' => $e->getMessage()]);
        }
    }

    public function searchGames(String $name)
    {
        $games = Game::where('name', 'LIKE', '%' . $name . '%')->get();
        if ($games->isEmpty())
            $list[] = null;
        foreach($games as $game)
        {
            $item = new Game;
            $item->id = $game->id;
            $item->bgg_game_id = $game->bgg_game_id;
            $item->name = $game->name;
            $list[] = $item->toArray();
        }
        return response(['success' => true, 'result' => $list], 200);
    }

    public function searchLetter(String $letter)
    {
        if (strlen($letter) != 1)
            return response(['success' => false, 'result' => "One letter expected"], 200);
        $games = Game::where('name', 'LIKE', $letter . '%')->get();
        if ($games->isEmpty())
            $list[] = null;
        foreach($games as $game)
        {
            $item = new Game;
            $item->id = $game->id;
            $item->bgg_game_id = $game->bgg_game_id;
            $item->name = $game->name;
            $list[] = $item->toArray();
        }
        return response(['success' => true, 'result' => $list], 200);
    }
}
