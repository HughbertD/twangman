@extends('layouts.master')
@section('content')
<div class="row" style="margin-bottom: 25px;">
    <div class="col-md-12">
        <h1 class="text-center">Twangman</h1>
    </div>

    <div class="col-md-12">
        <p class="lead text-center">Try to guess the Trending Twitter Topic</p>
    </div>

</div>

<div class="row" style="margin-bottom: 25px;">
    <div class="col-md-8 col-md-offset-2">
        <div id="wordContainer">
            <?php for($i = 0; $i < $numberTiles; $i++):?>
            <div class="tile tile-no-match" data-tile-position="<?=$i;?>">&nbsp;</div>
            <?php endfor;?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
            <img src="/images/hangman/0.png" id="hangingStage" class="center-block" data-hanging="0" />
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2" id="guessedLetters">
        <p class="center-block text-center"></p>
    </div>
</div>

<div class="row">
    <div class="col-md-2 col-md-offset-5" style="text-align: center;">
        <input type="text" maxlength="1" class="form-control input-sm" id="guess" style="width: 50px;" />
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-md-offset-5" style="text-align: center;">
        <img src="/images/restart.png" id="reset" alt="Restart the Game" />
    </div>
</div>
@stop