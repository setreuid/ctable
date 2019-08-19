<?php

function loadDocuments( $custcd, $board_idx, $page, $perid )
{
    $conn = getDBConnectionWithMysqli();
    $dbBoard = new DB_BOARD();

    $stmt = $conn->prepare( $dbBoard->load );
    $page = $page * 30;
    $stmt->bind_param( "ssssi", $perid, $perid, $custcd, $board_idx, $page );

    if( $stmt->execute() )
    {
        eval( MAKEDATA_FOR_ENCODED );

        if( isset( $data ) ) {
            successOutWithData( $data );
        } else {
            errorOut();
        }
    }
    else
    {
        errorOut();
    }

    $stmt->close();
    $conn->close();
}

function loadLastestDocuments( $custcd, $board_idx, $lastid, $perid )
{
    $conn = getDBConnectionWithMysqli();
    $dbBoard = new DB_BOARD();

    $stmt = $conn->prepare( $dbBoard->load_last );
    $stmt->bind_param( "ssssi", $perid, $perid, $custcd, $board_idx, $lastid );

    if( $stmt->execute() )
    {
        eval( MAKEDATA_FOR_ENCODED );

        if( isset( $data ) ) {
            successOutWithData( $data );
        } else {
            errorOut();
        }
    }
    else
    {
        errorOut();
    }

    $stmt->close();
    $conn->close();
}

function checkOnAIR()
{
    $agent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Accept: application/vnd.twitchtv.v3+json';

    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL,"https://api.twitch.tv/kraken/streams/setreuid");
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_SSLVERSION, 1);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_POST, 0);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt ($ch, CURLOPT_REFERER, 'https://api.twitch.tv');
    curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
    $buffer = curl_exec ($ch);
    $cinfo = curl_getinfo($ch);
    curl_close($ch);

	$buffer = "null";

    successOutWithData( $buffer );
}

?>
