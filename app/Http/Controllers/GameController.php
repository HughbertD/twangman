<?php
namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Twangman\Game;

class GameController extends Controller
{

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function start()
    {
        //Start a new game
        $this->game->start();
    }

}