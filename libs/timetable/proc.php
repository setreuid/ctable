<?php

require_once( "save.php" );
require_once( "load.php" );

function procTimeTable( $PARAMS )
{
    switch( $PARAMS->method )
    {
        case 'SAVE':

            saveTimeTable(
                $PARAMS->perid,
                $PARAMS->custcd,
                $PARAMS->year,
                $PARAMS->class,
                base64_encode( $PARAMS->data )
            );

        break;

        case 'LOAD':

            loadTimeTable(
                $PARAMS->perid,
                $PARAMS->custcd,
                $PARAMS->year,
                $PARAMS->class
            );

        break;
    }
}

?>
