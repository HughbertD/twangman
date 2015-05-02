$(function(){
    //$('.tile').transition({ //rotate the tiles, we are ready to play!
    //    perspective: '100px',
    //    rotateY: '180deg'
    //},
    //function(){
    //    $(this).css('transform', '');
    //});
    //$('#guess').focus();

    //detecting and dealing with input
    var  inputGuess = $('#guess');
    inputGuess.keypress(function(e)
    {
        var guessLetter = inputGuess.val();
        var code=(e.keyCode ? e.keyCode : e.which);
        if (code == 13){//Enter key detected, that's a guess
            $.post("/guess/"+guessLetter, {},
                function(response) {
                    switch(response.state) {
                        case 'match':
                            revealMatchingTiles(response.matchPositions, response.letter);
                            appendGuess(response.letter, response.state);
                        break;

                        case 'no-match':
                            advanceHanging();
                            appendGuess(response.letter, response.state);
                        break;

                        case 'dead':
                            advanceHanging();
                            endGame();
                        break;

                        case 'winner':
                            revealMatchingTiles(response.matchPositions, response.letter);
                            winGame();
                        break;
                    }
                },
                "json"
            );
            $('#guess').val(''); //clear out the guess from the input
        }
    });

    $('img#reset').on('click', function(){
        if(window.confirm('Reset the game and start again?')){
            location.reload()
        }
    });
});

revealMatchingTiles = function(matchPositions, letter) {
    for(var i in matchPositions) {
        $("div.tile[data-tile-position='"+matchPositions[i]+"']")
            .text(letter)
            .addClass('tile-match')
            .removeClass('tile-no-match');
    }
};

advanceHanging = function() {
    var hangingImg = $('#hangingStage');
    var nextHangingStage = hangingImg.data('hanging')+1;
    hangingImg.attr('src', "/images/hangman/"+nextHangingStage+".png");
    hangingImg.data('hanging', nextHangingStage);
};

endGame = function() {
    if(window.confirm('You lost sorry, play again?')){
        location.reload();
    }
};

winGame = function() {
    if(window.confirm('You won! Play again?')){
        location.reload();
    }
};

appendGuess = function(letter, letterClass) {
    $('div#guessedLetters > p').append("<span class='"+letterClass+"'>"+letter+"</span>");
};
