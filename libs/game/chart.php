<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

// apc_delete( 'GAME_CHART' );

if( apc_exists( 'GAME_CHART' ))
{
    $game = apc_fetch( 'GAME_CHART' );

    if( getTime() > $game[ 'edTime' ] )
    {
        $game = makeGame();
        apc_store( 'GAME_CHART', $game, $game[ 'ttl' ] );
    }
}
else
{
    $game = makeGame();
    apc_store( 'GAME_CHART', $game, $game[ 'ttl' ] );
}


function getTime()
{
    return floor( microtime( true ) * 1000 );
}

function makeGame()
{
    $key = rand( 0, 100 );
    $rnd = 0;

    if( $key < 2 ) { $rnd = 198; }
    else if( $key < 5 ) { $rnd = 95; }
    else if( $key < 10 ) { $rnd = 50; }
    else if( $key < 20 ) { $rnd = 40; }
    else if( $key < 50 ) { $rnd = 30; }
    else { $rnd = 20; }

    $stTime = getTime() + 7000;
    $edTime = getTime() + rand( 0, $rnd ) * 1000 + 7000;
    $ttl = ( $edTime - $stTime ) / 1000 + 5;

    return array(
        'stTime' => $stTime,
        'edTime' => $edTime,
        'ttl' => $ttl
    );
}

header('Content-Type: application/json');
print json_encode( $game );

?>
