<?php

function loadLiveStock( $flag, $token )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $stmt = $conn->prepare( $dbStock->live );

    $code = $token;
    $token = '%' . $token . '%';
    $stmt->bind_param( "sss", $flag, $token, $code );

    if( $stmt->execute() )
    {
        eval( MAKEDATA );

        if( isset( $data ))
        {
            successOutWithData( $data );
        }
        else
        {
            errorOut();
        }
        // eval( MAKEDATA_FOR_ENCODED );
    }
    else
    {
        errorOut();
    }

    $stmt->close();
    $conn->close();
}

function loadLiveUpStock( $flag )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $stmt = $conn->prepare( $dbStock->live_up );
    $stmt->bind_param( "s", $flag );

    if( $stmt->execute() )
    {
        eval( MAKEDATA );

        if( isset( $data ))
        {
            successOutWithData( $data );
        }
        else
        {
            errorOut();
        }
        // eval( MAKEDATA_FOR_ENCODED );
    }
    else
    {
        errorOut();
    }

    $stmt->close();
    $conn->close();
}

function loadLiveMyStock( $uid )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $stmt = $conn->prepare( $dbStock->mylive );
    $stmt->bind_param( "s", $uid );

    if( $stmt->execute() )
    {
        eval( MAKEDATA );

        if( isset( $data ))
        {
            successOutWithData( $data );
        }
        else
        {
            errorOut();
        }
        // eval( MAKEDATA_FOR_ENCODED );
    }
    else
    {
        errorOut();
    }

    $stmt->close();
    $conn->close();
}

?>
