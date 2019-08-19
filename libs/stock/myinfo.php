<?php

function getMyInfo( $uid )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $item = iquery( $conn, $dbStock->q_my_info, array(
                $uid,
            ));

    if( $item )
    {
        successOutWithData( $item );
    }
    else
    {
        errorOut();
    }

    $conn->close();
}

function loadRank()
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $stmt = tfquery( $conn, $dbStock->q_rank );

    if( $stmt )
    {
        $res = array();
        while( $x = $stmt->fetch_array( MYSQLI_ASSOC ))
        {
            if( $x )
            {
                $x[ 'tier' ] = getTier( $x[ 'stmoney' ] );
                $res[] = $x;
            }
        }
        successOutWithData( $res );
    }
    else
    {
        errorOut();
    }

    $conn->close();
}

function resetUserStock( $perid )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    fquery( $conn, $dbStock->q_reset_user_stock1, array(
        $perid
    ));

    fquery( $conn, $dbStock->q_reset_user_stock2, array(
        $perid
    ));

    successOut();

    $conn->close();
}

function setMoney( $perid, $money )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    fquery( $conn, $dbStock->q_detail_buy_now_user, array(
        $money,
        $perid
    ));

    successOut();

    $conn->close();
}

?>
