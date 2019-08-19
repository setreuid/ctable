<?php

/*
 *------------------------------------------------------------------------------
 * 공용 인라인 구문
 *------------------------------------------------------------------------------
 *
 * @작성자 조태호
 * @작성일 2016. 06. 29.
 *
 */
define( "MAKEDATA", "\$row=stmt_bind_assoc_v2(\$stmt);while(\$stmt->fetch()){foreach(\$row as \$key=>\$val){\$c[\$key]=\$val;}\$data[]=\$c;}" );
define( "MAKEDATA_FOR_ENCODED", "\$row=stmt_bind_assoc_v2(\$stmt);while(\$stmt->fetch()){foreach(\$row as \$key=>\$val){if(\$key==='data')\$val=base64_decode(\$val);\$c[\$key]=\$val;}\$data[]=\$c;}" );
define( "UNIQUEMAKEDATA", "\$row = array(); stmt_bind_assoc( \$stmt, \$row ); \$fetch_result = \$stmt->fetch();" );
define( "UNIQUEMAKEDATA4OBJECT", "\$row = stmt_bind_assoc_for_object( \$stmt );" );
define( "MAKEDATA_FOR_EDITORPICK", "\$row = array(); stmt_bind_assoc( \$stmt, \$row ); \$stmt->fetch(); \$row['data'] = base64_decode(\$row['data']);" );
define( "MAKEDATA_FOR_EDITORPICK_COMMENT", "\$row = array(); stmt_bind_assoc( \$stmt, \$row ); \$stmt->fetch(); \$row['contents'] = base64_decode(\$row['contents']);" );

/*
 *------------------------------------------------------------------------------
 * 공용 함수
 *------------------------------------------------------------------------------
 *
 * @작성자 조태호
 * @작성일 2016. 06. 29.
 *
 */
function now()
{
  return date( "YmdHi" );
}

function utf8_length( $str )
{
  $len = strlen( $str );
  for ( $i = $length = 0; $i < $len; $length++ )
  {
   $high = ord( $str{$i} );
   if ( $high < 0x80 )                    // 0<= code <128 범위의 문자(ASCII 문자)는 인덱스 1칸이동
    $i += 1;
   else if ($high < 0xE0)                 // 128 <= code < 224 범위의 문자(확장 ASCII 문자)는 인덱스 2칸이동
    $i += 2;
   else if ($high < 0xF0)                 // 224 <= code < 240 범위의 문자(유니코드 확장문자)는 인덱스 3칸이동
    $i += 3;
   else                                   // 그외 4칸이동 (미래에 나올문자)
    $i += 4;
  }
  return $length;
}

function utf8_strcut( $str, $chars, $tail = '...' )
{
  if ( utf8_length( $str ) <= $chars )    // 전체 길이를 불러올 수 있으면 tail을 제거한다.
   $tail = '';
  else
   $chars -= utf8_length( $tail );        // 글자가 잘리게 생겼다면 tail 문자열의 길이만큼 본문을 빼준다.

  $len = strlen( $str );

  for ( $i = $adapted = 0; $i < $len; $adapted = $i )
  {
   $high = ord( $str{$i} );

   if ( $high < 0x80 )
    $i += 1;
   else if ( $high < 0xE0 )
    $i += 2;
   else if ( $high < 0xF0 )
    $i += 3;
   else
    $i += 4;

   if ( --$chars < 0 )
    break;
  }

  return trim( substr( $str, 0, $adapted )) . $tail;
}

function stmt_bind_assoc( &$stmt, &$out )
{
	$data = mysqli_stmt_result_metadata( $stmt );
	$fields = array();
	$out = array();
	$fields[ 0 ] = $stmt;
	$count = 1;

	while( $field = mysqli_fetch_field( $data ))
	{
		$fields[ $count ] = &$out[ $field->name ];
		$count++;
	}
	call_user_func_array( "mysqli_stmt_bind_result", $fields );
}

function stmt_bind_assoc_v2( &$stmt )
{
	$data = mysqli_stmt_result_metadata( $stmt );
	$fields = array();
	$out = array();
	$fields[ 0 ] = $stmt;
	$count = 1;

  $query = "\$stmt->bind_result( ";

	while( $field = mysqli_fetch_field( $data ))
	{
    $query .= "\$out[ '" . $field->name . "' ], ";
	}

  $query = substr( $query, 0, -2 ) . " );";
  eval( $query );

  return $out;
}

function stmt_bind_assoc_for_object( &$stmt )
{
	$meta = $stmt->result_metadata();
	$fields = $meta->fetch_fields();

  foreach($fields as $field) {
    $result[$field->name] = "";
    $resultArray[$field->name] = &$result[$field->name];
  }

  call_user_func_array(array($stmt, 'bind_result'), $resultArray);

  if( $stmt->fetch() !== true ) return null;
  $resultObject = new stdClass();

  foreach ($resultArray as $key => $value) {
      $resultObject->$key = $value;
  }

  return $resultObject;
}

function fquery( $conn, $sql, $vals = array() )
{
    return $conn->query( vsprintf( $sql, $vals ));
}

function tfquery( $conn, $sql )
{
    return $conn->query( $sql );
}

function iquery( $conn, $sql, $vals = array() )
{
    $results = $conn->query( vsprintf( $sql, $vals ));

    if (!$results) return false;

    $x = $results->fetch_array( MYSQLI_ASSOC );
    return $x;
}

function tquery( $conn, $sql )
{
    $results = $conn->query( $sql );
    $res = array();

    while( $x = $results->fetch_array( MYSQLI_ASSOC ))
    {
        if( $x ) $res[] = $x;
    }

    return $res;
}

function rquery( $conn, $sql, $vals = array() )
{
    $results = $conn->query( vsprintf( $sql, $vals ));

    if( !$results )
    {
        return false;
    }

    $res = array();
    while( $x = $results->fetch_array( MYSQLI_ASSOC ))
    {
        if( $x ) $res[] = $x;
    }

    return $res;
}

function getTier( $money )
{
    $tier = 0;
    if( $money < 1000001 )
    {
        // 흙 1
        $tier = 0;
    }
    else if( $money < 5000000 )
    {
        // 흙 2
        $tier = 1;
    }
    else if( $money < 10000000 )
    {
        // 흙 3
        $tier = 2;
    }
    else if( $money < 30000000 )
    {
        // 동 1
        $tier = 3;
    }
    else if( $money < 70000000 )
    {
        // 동 2
        $tier = 4;
    }
    else if( $money < 100000000 )
    {
        // 동 3
        $tier = 5;
    }
    else if( $money < 300000000 )
    {
        // 은 1
        $tier = 6;
    }
    else if( $money < 700000000 )
    {
        // 은 2
        $tier = 7;
    }
    else if( $money < 1000000000 )
    {
        // 은 3
        $tier = 8;
    }
    else if( $money < 3000000000 )
    {
        // 금 1
        $tier = 9;
    }
    else if( $money < 7000000000 )
    {
        // 금 2
        $tier = 10;
    }
    else if( $money < 10000000000 )
    {
        // 금 3
        $tier = 11;
    }
    else if( $money < 30000000000 )
    {
        // 플 1
        $tier = 12;
    }
    else if( $money < 70000000000 )
    {
        // 플 2
        $tier = 13;
    }
    else if( $money < 100000000000 )
    {
        // 플 3
        $tier = 14;
    }
    else if( $money < 300000000000 )
    {
        // 다 1
        $tier = 15;
    }
    else if( $money < 700000000000 )
    {
        // 다 2
        $tier = 16;
    }
    else if( $money < 1000000000000 )
    {
        // 다 3
        $tier = 17;
    }
    else
    {
        // 마스터
        $tier = 18;
    }

    return $tier;
}

/*
 *------------------------------------------------------------------------------
 * 최종 출력용 공용 함수
 *------------------------------------------------------------------------------
 *
 * @작성자 조태호
 * @작성일 2016. 06. 29.
 *
 */
function errorOut()
{
  echo json_encode( array( "message" => "ERROR", "result" => "error" ));
}

function successOut()
{
  echo json_encode( array( "message" => "SUCCESS", "result" => "success" ));
}

function errorOutWithData( $query_result_data )
{
  echo json_encode( array( "message" => "ERROR", "result" => "error", "data" => $query_result_data ));
}

function successOutWithData( $query_result_data )
{
  echo json_encode( array( "message" => "SUCCESS", "result" => "success", "data" => $query_result_data ));
}

?>
