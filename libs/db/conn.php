<?php

/**
* getDBConnectionWithMysqli
*  Mysqli DB 커넥션 받아오는 함수
* @author 조태호
* @return [Mysqli] [접속 실패의 경우 === false]
*/
function getDBConnectionWithMysqli()
{
    //////////////////////////////////
    // DB CONN INFOMATION
    $dbhost   = "127.0.0.1";
    $dbname   = "CTABLE";
    $dbid     = "CTABLE";
    $dbpw     = "DBPASS";
    //////////////////////////////////

    $conn = new mysqli( $dbhost, $dbid, $dbpw, $dbname );
    $conn->set_charset( "utf8" );

    if ( $conn )
    {
        return $conn;
    }
    else
    {
        $conn->close();
        return false;
    }
}

?>
