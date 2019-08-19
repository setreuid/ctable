<?php

function loadTimeTable( $perid, $custcd, $year, $class )
{
    $conn = getDBConnectionWithMysqli();
    $dbTimeTable = new DB_TIMETABLE();

    if ($class === '21'
        && date('m')*1 < 3) {
        $year--;
    }

    $stmt = $conn->prepare( $dbTimeTable->load );
    $stmt->bind_param( "ssss", $perid, $custcd, $year, $class );

    if( $stmt->execute() )
    {
        $stmt->bind_result( $data );
        if( $obj = $stmt->fetch() )
        {
            $data = base64_decode( $data );
            checkAccountForOutput( $perid, $data );
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

function checkAccountForOutput( $perid, $data )
{
    $conn = getDBConnectionWithMysqli();
    $dbTimeTable = new DB_TIMETABLE();

    $stmt = $conn->prepare( $dbTimeTable->check );
    $stmt->bind_param( "s", $perid );

    if( $stmt->execute() )
    {
        if( $stmt->fetch() ) {
            successOutWithData( htmlspecialchars_decode( $data ) );
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

?>
