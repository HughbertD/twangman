<?php

use App\Twangman\Game;

class GameControllerTest extends TestCase
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
     * Test /new route
     */
    public function testGameStart()
    {
        $this->app->bind('App\Twangman\Game', function(){
            $trends = [
                (object) ['trends' => [
                    (object) ['name' => 'TestingTwitterTopic']
                ]]
            ];
            $twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
                ->disableOriginalConstructor()
                ->getMock();
            $twitterOAuth->method('get')->willReturn($trends);

            $game = new Game($twitterOAuth);
            $game->start();
            return $game;
        });
        $this->call('GET', '/new');
        $this->assertResponseOk();
        $this->assertSessionHas('game');
    }

    /**
     * Test the /guess route
     */
    public function testGameGuessCorrect()
    {
        $this->app->bind('App\Twangman\Game', function(){
            $trends = [
                (object) ['trends' => [
                    (object) ['name' => 'TestingTwitterTopic']
                ]]
            ];
            $twitterOAuth = $this->getMockBuilder('Abraham\TwitterOAuth\TwitterOAuth')
                ->disableOriginalConstructor()
                ->getMock();
            $twitterOAuth->method('get')->willReturn($trends);

            $game = new Game($twitterOAuth);
            $game->start();
            return $game;

        });
        $response = $this->call('POST', '/guess/t');
        $this->assertResponseOk($response);

        $responseContent = json_decode($response->getContent());
        $this->assertTrue($responseContent->success);
        $this->assertEquals($responseContent->letter,  'T');
        $this->assertFalse($responseContent->state == Game::DEAD_STATE);
        $this->assertEquals($responseContent->matchPositions, [0,3,7,10,11,14]);
    }


}