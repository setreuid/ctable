<?php

require_once( "getLiveStock.php" );
require_once( "detailStock.php" );
require_once( "myinfo.php" );

function procStock( $PARAMS )
{
    switch( $PARAMS->method )
    {
        case 'LIVE':
        {
            if( !isset( $PARAMS->token ) || $PARAMS->token == '' )
            {
                loadLiveUpStock(
                    $PARAMS->flag
                );
            }
            else
            {
                loadLiveStock(
                    $PARAMS->flag,
                    $PARAMS->token
                );
            }
        } break;

        case 'MYLIVE':
        {
            loadLiveMyStock(
                $PARAMS->user
            );
        } break;

        case 'DETAIL_BUY':
        {
            loadDetailForBuy( $PARAMS->user, $PARAMS->code );
        } break;

        case 'BUY_NOW':
        {
            stockBuyNow( $PARAMS->user, $PARAMS->code, $PARAMS->counts );
        } break;

        case 'DETAIL_PAY':
        {
            loadDetailForPay( $PARAMS->user, $PARAMS->code );
        } break;

        case 'PAY_NOW':
        {
            stockPayNow( $PARAMS->user, $PARAMS->code, $PARAMS->counts );
        } break;

        case 'UPD':
        {
            setMoney( $PARAMS->user, $PARAMS->key );
        } break;

        case 'MYINFO':
        {
            getMyInfo( $PARAMS->user );
        } break;

        case 'RANK':
        {
            loadRank();
        } break;

        case 'RESET':
        {
            resetUserStock( $PARAMS->user );
        } break;
    }
}

?>
