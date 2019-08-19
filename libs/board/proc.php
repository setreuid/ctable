<?php

require_once( "new_document.php" );
require_once( "modify_document.php" );
require_once( "load_document.php" );

function procBoard( $PARAMS )
{
    switch( $PARAMS->method )
    {
        case 'NEW':

            if( trim( $PARAMS->data ) == '' )
            {
                errorOut();
                return;
            }

            newDocument(
                $PARAMS->perid,
                $PARAMS->custcd,
                $PARAMS->board_idx,
                base64_encode( htmlspecialchars( $PARAMS->data ))
            );

        break;

        case 'LOAD':

            loadDocuments(
                $PARAMS->custcd,
                $PARAMS->board_idx,
                $PARAMS->page,
                $PARAMS->perid
            );

        break;

        case 'LOAD_LAST':

            loadLastestDocuments(
                $PARAMS->custcd,
                $PARAMS->board_idx,
                $PARAMS->lastid,
                $PARAMS->perid
            );

        break;

        case 'REMOVE':

            removeDocument(
                $PARAMS->id
            );

        break;

        case 'LIKE':

            likeDocument(
                $PARAMS->custcd,
                $PARAMS->perid,
                $PARAMS->id,
                $PARAMS->like
            );

        break;

        case 'ONAIR':

            checkOnAIR();

        break;
    }
}

?>
