<?php
namespace App\Http\Controllers;

use App\Twangman\Game;
use Illuminate\Http\Response;
use Exception;

class GameController extends Controller
{

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Start a new instance of a game
     */
    public function start()
    {
        try{
            $this->game->start();
        } catch(Exception $e) {
            die($e->getMessage());
        }
        return view('game', ['numberTiles' => $this->game->numberTiles]);
    }

    /**
     * Take a guess with a letter
     * @param $letter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function guess($letter)
    {
        try{
            $loaded = $this->game->load(session('game'));
        } catch(Exception $e) {
            return Response::create([], 404);
        }

        $matches = $this->game->guess($letter);
        $success = !empty($matches);
        $state = $this->game->state($success);

        $response = [
            'success' => !empty($matches),
            'letter' => strtoupper($letter),
            'matchPositions' => $matches,
            'state' => $state
        ];

        $this->game->save();
        if($state == Game::DEAD_STATE || $state == Game::WINNER_STATE) {
            $this->game->end();
        }

        return Response::create($response, 200);
    }

}