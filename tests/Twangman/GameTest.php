<?php

use App\Twangman\Game;

class GameTest extends TestCase
{

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    public function createTwitterMock($twitterTopic)
    {
        $trends = [
            (object) ['trends' => [
                (object) ['name' => $twitterTopic]
            ]]
        ];
        $twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
            ->disableOriginalConstructor()
            ->getMock();
        $twitterOAuth->method('get')->willReturn($trends);
        return $twitterOAuth;
    }

    /**
     * Test the game start up method
     */
    public function testGameStart()
    {
        $twitterTopic = 'TestTwitterTopic';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);

        $game = new Game($twitterOAuth);
        $game->start();

        $this->assertEquals($game->word, 'TESTTWITTERTOPIC');
        $this->assertEquals($game->numberTiles, strlen($twitterTopic));
    }

    public function testNumberOfTiles()
    {
        $twitterTopic = 'Three Words Topic';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);

        $game = new Game($twitterOAuth);
        $game->start();

        $this->assertEquals($game->numberTiles, 15);
    }

    public function testGameGuessCorrect()
    {
        $twitterTopic = 'abcdefg';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);

        $game = new Game($twitterOAuth);
        $game->start();
        $matches = $game->guess('a');
        $this->assertEquals($matches, [0=>0]);
        $this->assertTrue(in_array('A', $game->correctGuesses));
        $this->assertTrue(empty($game->incorrectGuesses));
    }

    public function testGameGuessIncorrect()
    {
        $twitterTopic = 'abcdefg';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);

        $game = new Game($twitterOAuth);
        $game->start();
        $matches = $game->guess('h');
        $this->assertEquals($matches, []);
        $this->assertFalse(in_array('H', $game->correctGuesses));
        $this->assertTrue(in_array('H', $game->incorrectGuesses));
    }

    public function testGameGuessEmpty()
    {
        $twitterTopic = 'abcdefg';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);
        $game = new Game($twitterOAuth);
        $game->start();
        $matches = $game->guess('');
        $this->assertTrue(empty($matches));
    }

    public function testGameGuessToDeath()
    {
        $twitterTopic = 'ZYXWVUTS';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);

        $game = new Game($twitterOAuth);
        $game->start();
        foreach(['A','B','C','D','E'] as $guess) {
            $game->guess($guess);
            $this->assertFalse($game->isDead());
        }
        //Final nail in the coffin
        $game->guess('I');
        $this->assertTrue($game->isDead());
    }

    public function testGameGuessToWin()
    {
        $twitterTopic = 'ABCDEFGHI';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);

        $game = new Game($twitterOAuth);
        $game->start();
        foreach(['A','B','C','D','E','F','G','H'] as $guess) {
            $game->guess($guess);
            $this->assertFalse($game->isDead());
        }
        //FTW.
        $game->guess('I');

        $this->assertFalse($game->isDead());
        $this->assertTrue($game->isWinner());

    }

    public function testGameGuessToWinWithRepeatedLetters()
    {
        $twitterTopic = 'ABBCDEFFGHI';
        $twitterOAuth = $this->createTwitterMock($twitterTopic);

        $game = new Game($twitterOAuth);
        $game->start();
        foreach(['A','B','C','D','E','F','G','H'] as $guess) {
            $game->guess($guess);
            $this->assertFalse($game->isDead());
            $this->assertFalse($game->isWinner());
        }
        //FTW!
        $game->guess('I');

        $this->assertFalse($game->isDead());
        $this->assertTrue($game->isWinner());

    }
}