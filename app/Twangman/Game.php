<?php

namespace App\Twangman;

use Abraham\TwitterOAuth\TwitterOAuth;
use Exception;

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
     * Total lives (guesses) before you are hung!
     */
    const LIVES = 6;

    /**
     * Player is dead
     */
    const DEAD_STATE = "dead";

    /**
     * Player won
     */
    const WINNER_STATE = "winner";

    /**
     * Player next turn, found match
     */
    const MATCH_FOUND_STATE = "match";

    /**
     * Player next turn, no found match
     */
    const NO_MATCH_FOUND_STATE = "no-match";

    /**
     *
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
        if(!is_array($availableTrends)) {
            throw new Exception("Couldn't retrieve Twitter topics");
        }

        //Pick a trending topic at random, set as our gameWord
        $gameWord = $this->pickWord($availableTrends[0]->trends);
        $this->setWord($gameWord);

        $this->numberTiles = $this->setNumberTiles($gameWord);

        //Store the game data to the session
        $this->save();
    }

    /**
     * Save the current game back to the session
     *
     */
    public function save()
    {
        session(['game' => $this]);
    }

    /**
     * Load the current game from the session
     * @param $game
     * @return bool
     * @throws Exception
     */
    public function load($game)
    {
        if(is_null($game)){
            throw new Exception("Cannot load game");
        }

        $this->setWord($game->word);
        $this->setNumberTiles($game->word);
        $this->correctGuesses = $game->correctGuesses;
        $this->incorrectGuesses = $game->incorrectGuesses;
        $this->numberGuessesTaken = $game->numberGuessesTaken;
        return true;
    }

    /**
     * Finish a game
     */
    public function end()
    {
        session(['game' => NULL]);
    }

    public function guess($guess)
    {
        $guess = strtoupper($guess);
        $wordArray = str_split($this->word);
        $matches = [];
        foreach($wordArray as $pos => $letter) {
            if($letter == $guess) $matches[] = $pos;
        }

        $this->assignGuess($guess, !empty($matches));
        return $matches;
    }

    /**
     * Route the guess to the correct setter
     * @param $guess
     * @param $correct
     */
    public function assignGuess($guess, $correct)
    {
        ($correct === true) ? $this->addCorrectGuess($guess) : $this->addIncorrectGuess($guess);
    }

    /**
     * Add a guess to the correctGuesses var
     * @param $guess
     */
    public function addCorrectGuess($guess)
    {
        $this->correctGuesses[] = $guess;
    }

    /**
     * Add a guess to the incorrectGuesses var, dock a life
     * @param $guess
     * @return Array
     */
    public function addIncorrectGuess($guess)
    {
        array_push($this->incorrectGuesses, $guess);
        return $this->incorrectGuesses;
    }

    /**
     * Gets the state of the player after their guess
     * @return string
     */
    public function state($success)
    {
        if($this->isDead()) {
            return self::DEAD_STATE;
        }

        if($this->isWinner()) {
            return self::WINNER_STATE;
        }

        if($success) {
            return self::MATCH_FOUND_STATE;
        }

        return self::NO_MATCH_FOUND_STATE;
    }

    /**
     * Check if the player has run out of guesses
     * @return bool
     */
    public function isDead()
    {
        return count($this->incorrectGuesses) >= self::LIVES;
    }

    /**
     * Check if the player has won
     * @return bool
     */
    public function isWinner()
    {
        $leftToGuess = array_diff(str_split($this->word), $this->correctGuesses);
        return empty($leftToGuess);
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

    /**
     * Setter for word
     * @param $word
     */
    private function setWord($word)
    {
        $this->word = strtoupper($word);
    }

    /**
     * Set the number of tiles needed to render on screen
     * @param $gameWord
     * @return int
     */
    private function setNumberTiles($gameWord)
    {
        $gameWord = preg_replace("/[^A-Za-z0-9]/", '', $gameWord);
        return strlen($gameWord);
    }



}