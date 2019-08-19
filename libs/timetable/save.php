<?php

function saveTimeTable( $perid, $custcd, $year, $class, $data )
{
    $conn = getDBConnectionWithMysqli();
    $dbTimeTable = new DB_TIMETABLE();

    // $stmt = $conn->prepare( $dbTimeTable->load );
    // $stmt->bind_param( "ssss", $perid, $custcd, $year, $class );
    $stmt = $conn->prepare( $dbTimeTable->remove );
    $stmt->bind_param( "ssss", $perid, $custcd, $year, $class );

    if( $stmt->execute() )
    {
        newTimeTable( $perid, $custcd, $year, $class, $data );
        // if( $stmt->fetch() !== true )
        //     newTimeTable( $perid, $custcd, $year, $class, $data );
        // else
        //     updateTimeTable( $perid, $custcd, $year, $class, $data );
    }
    else
    {
        errorOut();
    }

    $stmt->close();
    $conn->close();
}

function newTimeTable( $perid, $custcd, $year, $class, $data )
{
    $conn = getDBConnectionWithMysqli();
    $dbTimeTable = new DB_TIMETABLE();

    $stmt = $conn->prepare( $dbTimeTable->new );
    $null = NULL;
    $stmt->bind_param( "ssbss", $perid, $custcd, $null, $year, $class );
    $stmt->send_long_data( 2, $data );

    if ( $stmt->execute() )
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

function updateTimeTable( $perid, $custcd, $year, $class, $data )
{
    $conn = getDBConnectionWithMysqli();
    $dbTimeTable = new DB_TIMETABLE();

    $stmt = $conn->prepare( $dbTimeTable->update );
    $null = NULL;
    $stmt->bind_param( "bssss", $null, $perid, $ucstcd, $year, $class );
    $stmt->send_long_data( 0, $data );

    if ( $stmt->execute() )
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

?>
