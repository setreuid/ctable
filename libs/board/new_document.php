<?php

function newDocument( $perid, $custcd, $board_idx, $data )
{
    $conn = getDBConnectionWithMysqli();
    $dbBoard = new DB_BOARD();
    $dbStock = new DB_STOCK();

    $stmt = $conn->prepare( $dbBoard->new );
    $null = NULL;
    $now = now();
    $addr = $_SERVER['REMOTE_ADDR'];

    $item = iquery( $conn, $dbStock->q_my_stmoney, array(
                $perid
            ));

    $user = iquery( $conn, $dbBoard->get_my_info, array(
                $perid
            ));

    $money = $item[ 'stmoney' ];
    $tier = getTier( $money );

    $none = '';
    $stmt->bind_param( "sssbsssssss", $perid, $custcd, $board_idx, $null, $now, $none, $none, $none, $addr, $tier, $user[ 'nick' ] );
    $stmt->send_long_data( 3, $data );

    if ( $stmt->execute() )
    {
        successOutWithData( $stmt->insert_id );
    }
    else
    {
        errorOutWithData( $conn->error );
    }

    $stmt->close();
    $conn->close();
}

?>
