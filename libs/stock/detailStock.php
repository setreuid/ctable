<?php

function loadDetailForBuy( $uid, $code )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $stmt = $conn->prepare( $dbStock->detail_buy );

    $stmt->bind_param( "sss", $uid, $code, $code );

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

function loadDetailForPay( $uid, $code )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $stmt = $conn->prepare( $dbStock->detail_pay );
    $stmt->bind_param( "ss", $uid, $code );

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

function stockBuyNow( $uid, $code, $counts )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $item = iquery( $conn, $dbStock->q_detail_buy, array(
                $uid,
                $code,
                $code
            ));

    if( $counts * $item[ 'cost' ] > $item[ 'money' ] )
    {
        errorOut();
    }
    else
    {
        fquery( $conn, $dbStock->q_detail_buy_now_user, array(
            $item[ 'money' ] - $counts * $item[ 'cost' ],
            $uid
        ));

        $info = iquery( $conn, $dbStock->q_isexist_stock, array(
                    $uid,
                    $code,
                    $code
                ));

        if( $info )
        {
            fquery( $conn, $dbStock->q_detail_buy_exist, array(
                $info[ 'counts' ] + $counts,
                $item[ 'cost' ] * $counts + $info[ 'cost' ],
                $uid,
                $code
            ));
        }
        else
        {
            fquery( $conn, $dbStock->q_detail_buy_new, array(
                $uid,
                $code,
                $counts,
                $counts * $item[ 'cost' ]
            ));
        }

        successOut();
    }

    $conn->close();
}

function stockPayNow( $uid, $code, $counts )
{
    $conn = getDBConnectionWithMysqli();
    $dbStock = new DB_STOCK();

    $item = iquery( $conn, $dbStock->q_detail_pay, array(
                $uid,
                $code,
                $code
            ));

    if( $counts > $item[ 'counts' ] )
    {
        errorOut();
    }
    else
    {
        fquery( $conn, $dbStock->q_detail_pay_now_user, array(
            $item[ 'money' ] + $counts * $item[ 'cost' ],
            $uid
        ));

        if( $item[ 'counts' ] == $counts )
        {
            fquery( $conn, $dbStock->q_detail_pay_remove, array(
                $uid,
                $code
            ));
        }
        else
        {
            fquery( $conn, $dbStock->q_detail_pay_update, array(
                $item[ 'counts' ] - $counts,
                $item[ 'stcost' ] - $counts * $item[ 'cost' ],
                $uid,
                $code
            ));
        }

        successOut();
    }

    $conn->close();
}

?>
