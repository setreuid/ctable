<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

header( "Access-Control-Allow-Origin : *" );
require_once( "../db/conn.php" );

define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

/*
 *------------------------------------------------------------------------------
 * Editor Pick 파일 업로드
 *------------------------------------------------------------------------------
 *
 * @param  upload {FILES}
 * @return        {JSON}  { uploaded, filename, url, [error] }
 *
 */

if( !isset( $_FILES['upload'] ))
{
    errorOut( "업로드 할 파일이 없습니다." );
    exit(0);
}

if( $_FILES['file']['size'] > 20*MB )
{
    errorOut( "이미지 크기(" . floor($_FILES['file']['size'] / MB) . "MB)가 20MB를 넘으면 안됩니다." );
    exit(0);
}

$conn = getDBConnectionWithMysqli();
// DB 연결

$sql_insert_image = "Insert Into BOARD_IMAGE ( data, board_idx ) Values ( ?, ? )";
$stmt = $conn->prepare( $sql_insert_image );

$data = file_get_contents( $_FILES['upload']['tmp_name'] );
// 이미지 데이터 가공

$null = NULL;
//

$stmt->bind_param( "bi", $null, $_REQUEST[ "board_idx" ] );
$stmt->send_long_data( 0, $data );

if ( $stmt->execute() )
{
  successOut( $stmt->insert_id );
}
else
{
  errorOut();
}

$stmt->close();
mysqli_close( $conn );
// DB 종료

/*
 *------------------------------------------------------------------------------
 * successOut
 *------------------------------------------------------------------------------
 *
 * @author 조태호
 *
 * 정상 업로드의 경우
 *
 */

function successOut( $fileIdx )
{
  echo json_encode( array(
    "uploaded" => 1
  ));
} // successOut

/*
 *------------------------------------------------------------------------------
 * errorOut
 *------------------------------------------------------------------------------
 *
 * @author 조태호
 *
 * 업로드 에러
 *
 */

function errorOut( $message )
{
  echo json_encode( array(
    "uploaded" => 0,
    "error" => array(
      "message" => $message
    )
  ));
} // errorOut

?>
