<?php

namespace App\Twangman;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class Game
 * @package App\Twangman
 * Manages the instance of the game
 */
class Game
{
    /**
     * Used to retrieve the available trending topics
     * @var TwitterOAuth
     */
    private $oAuth;

    /**
     * The Word to be guessed by the player
     * @var string
     */
    public $word = '';

    /**
     * Number of tiles the word has
     * @var int
     */
    public $numberTiles = 0;

    /**
     * Guesses the player has taken
     * @var int
     */
    public $numberGuessesTaken = 0;

    /**
     * Correct letters guesses by the player so far
     * @var array
     */
    public $correctGuesses = [];

    /**
     * Incorrect guesses guessed by the user so far
     * @var array
     */
    public $incorrectGuesses = [];

    /**
     * @param TwitterOAuth $oAuth
     */
    public function __construct(TwitterOAuth $oAuth)
    {
        $this->oAuth = $oAuth;
    }

    /**
     * Start a new game
     */
    public function start()
    {
        //Get the current trending topics (top10)
        $availableTrends = $this->oAuth->get('trends/place', ['id' => 23424975]);

        //Pick a trending topic at random
        $gameWord = $this->pickWord($availableTrends[0]->trends);

        $this->word = $gameWord;

        $this->numberTiles = strlen($gameWord);

        //Store the game data to the session
        session(['game' => $this]);
    }

    /**
     * @param array $availableTrends
     * @return string
     */
    private function pickWord(Array $availableTrends)
    {
        shuffle($availableTrends);
        return current($availableTrends)->name;
    }


}