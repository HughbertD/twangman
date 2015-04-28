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

    /**
     * Test the game start up method
     */
    public function testGameStart()
    {

        $twitterTopic = 'TestTwitterTopic';
        $trends = [
            (object) ['trends' => [
                (object) ['name' => $twitterTopic]
            ]]
        ];
        $twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
            ->disableOriginalConstructor()
            ->getMock();
        $twitterOAuth->method('get')->willReturn($trends);
        $game = new Game($twitterOAuth);
        $game->start();

        $this->assertEquals($game->word, $twitterTopic);
        $this->assertEquals($game->numberTiles, strlen($twitterTopic));
    }

}