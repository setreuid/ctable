<?php

// http://clc.chosun.ac.kr/ilos/lo/login.acl
// http://clc.chosun.ac.kr/ilos/main/main_form.acl
// http://clc.chosun.ac.kr/ilos/st/course/plan_form.acl?acl=plan_form.acl&s=menu
//

header( "Access-Control-Allow-Origin : *" );
header( "Access-Control-Allow-Headers : Origin, X-Requested-With, Content-Type, Accept" );
header( "Content-Type: application/json" );
header( "Content-Type: text/html; charset=UTF-8" );
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

// $PARAMS = new stdClass();
// $PARAMS->target = $_REQUEST['target'];
// $PARAMS->method = $_REQUEST['method'];
// $PARAMS->year = $_REQUEST['year'];

// $sys = explode( '-', trim( $PARAMS->target ) );

$agent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,"http://www2.chosun.ac.kr/mbs/chosun/jsp/academic_calender/academic_calender.jsp?academicIdx=25846&id=chosun_030102000000"); //접속할 URL 주소
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 인증서 체크같은데 true 시 안되는 경우가 많다.
curl_setopt ($ch, CURLOPT_SSLVERSION, 3); // SSL 버젼 (https 접속시에 필요)
curl_setopt ($ch, CURLOPT_HEADER, 0); // 헤더 출력 여부
curl_setopt ($ch, CURLOPT_POST, 0); // Post Get 접속 여부
curl_setopt ($ch, CURLOPT_TIMEOUT, 30); // TimeOut 값
curl_setopt ($ch, CURLOPT_REFERER, 'http://www2.chosun.ac.kr');
curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true); // 결과값을 받을것인지
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$buffer = curl_exec ($ch);
$cinfo = curl_getinfo($ch);
curl_close($ch);

$buffer = substr( $buffer, strpos( $buffer, "<div class=\"academic_calender\">" ) );
preg_match_all ( "|<li><span class=\"date\">([0-9\.]+) ~ ([0-9\.]+)</span>(.+)</li>|U", $buffer, $result );

$map = $result[0];
$res = array();
for( $i=0 ; $i<count($map) ; $i++ )
{
    if( $i > 0 && substr( $result[1][$i], 0, strrpos( $result[1][$i], "." )) !== substr( $result[1][$i-1], 0, strrpos( $result[1][$i-1], "." )) )
    {
        array_push( $res, array( "name" => $result[3][$i], "stdate" => $result[1][$i], "eddate" => $result[2][$i], "newline" => true ) );
    }
    else
    {
        array_push( $res, array( "name" => $result[3][$i], "stdate" => $result[1][$i], "eddate" => $result[2][$i], "newline" => false ) );
    }
}

echo json_encode( array( 'result' => 'success', 'message' => 'OK', 'data' => $res ) );

?>
