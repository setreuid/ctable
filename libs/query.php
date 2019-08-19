<?php

// ini_set('memory_limit','-1');
error_reporting(E_ALL);
ini_set("display_errors", 1);

header( "Access-Control-Allow-Origin : *" );
header( "Access-Control-Allow-Headers : Origin, X-Requested-With, Content-Type, Accept" );
header( "Content-Type: application/json" );
header( "x-content-type-options: nosniff" );

require_once( "lib/util.php" );
require_once( "getGeoInfomation.php" );

require_once( "db/conn.php" );
require_once( "db/timetable.php" );
require_once( "db/board.php" );
require_once( "db/stock.php" );

require_once( "timetable/proc.php" );
require_once( "board/proc.php" );
require_once( "stock/proc.php" );

$PARAMS = json_decode( file_get_contents( 'php://input' ));

foreach( $PARAMS as $key => $value )
{
    $PARAMS->$key = filter_var( $value, FILTER_SANITIZE_STRING );
}

switch ( $PARAMS->target )
{
    case "TIMETABLE" :

        procTimeTable( $PARAMS );

    break;

    case "BOARD" :

        procBoard( $PARAMS );

    break;

    case "STOCK" :

        procStock( $PARAMS );

    break;

    default :

        errorOutWithData( $PARAMS );

    break;
}

?>
