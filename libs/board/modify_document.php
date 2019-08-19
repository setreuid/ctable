<?php

function removeDocument( $index )
{
    $conn = getDBConnectionWithMysqli();
    $dbBoard = new DB_BOARD();

    $stmt = $conn->prepare( $dbBoard->remove );
    $stmt->bind_param( "i", $index );

    if( $stmt->execute() )
    {
        successOut();
    }
    else
    {
        errorOut();
    }

    $stmt->close();
    $conn->close();
}

function likeDocument( $custcd, $perid, $board_idx, $islike )
{
    $conn = getDBConnectionWithMysqli();
    $dbBoard = new DB_BOARD();

    $item = iquery( $conn, $dbBoard->has_like, array(
                $board_idx,
                $perid
            ));

    if ($item)
    {
        // Already exist
        fquery($conn, $dbBoard->set_like, array(
            $islike,
            $board_idx,
            $perid
        ));
    }
    else
    {
        fquery($conn, $dbBoard->new_like, array(
            $board_idx,
            $perid,
            $islike
        ));
    }

    $doc = iquery($conn, $dbBoard->get_like, array(
                $perid,
                $board_idx
            ));

    successOutWithData( $doc );

    $conn->close();
}

?>
