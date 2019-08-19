<?php

// http://clc.chosun.ac.kr/ilos/lo/login.acl
// http://clc.chosun.ac.kr/ilos/main/main_form.acl
// http://clc.chosun.ac.kr/ilos/st/course/plan_form.acl?acl=plan_form.acl&s=menu
//

header( "Access-Control-Allow-Origin : *" );
header( "Access-Control-Allow-Headers : Origin, X-Requested-With, Content-Type, Accept" );
header( "Content-Type: application/json" );
header("Content-Type: text/html; charset=UTF-8");
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

$PARAMS = json_decode( file_get_contents( 'php://input' ));

// $PARAMS = new stdClass();
// $PARAMS->target = $_REQUEST['target'];
// $PARAMS->method = $_REQUEST['method'];
// $PARAMS->year = $_REQUEST['year'];

if( !isset( $PARAMS->target ) )
{
    echo json_encode( array( 'result' => 'error', 'message' => '학번을 입력하지 않았습니다' ) );
    exit(1);
}

$sys = explode( '-', trim( $PARAMS->target ) );

$agent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,"http://wing.chosun.ac.kr/ch_stu?toinb_dataid4=sup1005we_sel02&SYear=" . $PARAMS->year . "&SSmt=" . $PARAMS->method . "&SCuriNum=" . $sys[0] . "&SCourseCls=" . $sys[1] . "&SGradDiv=01&Week=0"); //접속할 URL 주소
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 인증서 체크같은데 true 시 안되는 경우가 많다.
curl_setopt ($ch, CURLOPT_SSLVERSION, 3); // SSL 버젼 (https 접속시에 필요)
curl_setopt ($ch, CURLOPT_HEADER, 0); // 헤더 출력 여부
curl_setopt ($ch, CURLOPT_POST, 0); // Post Get 접속 여부
curl_setopt ($ch, CURLOPT_TIMEOUT, 30); // TimeOut 값
curl_setopt ($ch, CURLOPT_REFERER, 'http://wing.chosun.ac.kr');
curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true); // 결과값을 받을것인지
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$buffer = curl_exec ($ch);
$cinfo = curl_getinfo($ch);
curl_close($ch);

$raw = strToHex( $buffer );
$tok = strToHex( $sys[0] );
$tok .= "00" . strToHex( $sys[1] );
$tok .= "00";
$st = strpos( $raw, $tok ) + 18;
$body = substr( $raw, $st );
$zero = "0000000000000000";
$ed = strpos( $body, $zero );
$body = substr( $body, 0, $ed );
$body = zeroTornrf($body);

$rnrfTrimTok = "0D0A0D0A0D0A0D0A";
$body = str_replace( $rnrfTrimTok, "0D0A", $body );
$rnrfTrimTok = "0D0A0D0A0D0A";
$body = str_replace( $rnrfTrimTok, "0D0A", $body );

$body = hexToStr( $body );
$body = iconv( "euckr", "utf8", $body );
$body_spr = multiexplode( array( "\r\n", "\r", "\n" ), $body );

for( $index = 0 ; $index < count($body_spr) ; $index++ )
{
    $item = trim( $body_spr[$index] );

    if( $index == 0 ) $item = "과목명 : " . $item . " (" . $sys[1] . "분반)";
    else if( $index == 1 ) $item = "학점 : " . $item;
    else if( $index == 2 ) $item = "교수 : " . $item;
    else if( $index == 3 ) $item = "장소 : " . $item;

    $body_spr[$index] = $item;

    // if( $index < 8 ) $text = "<span>" . $text . "</span>";
    // if( $index === 8 ) $text = "<span>" . $text;
    //
    // $result .= $text;
}

$result = $result . "</span>";

echo json_encode( array( 'result' => 'success', 'message' => 'OK', 'data' => $body_spr ) );

function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}
function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}
function zeroTornrf( $dump )
{
    $i = 0;
    $ches = "";
    $length = strlen( $dump );

    while( $i < $length )
    {
        $ches = $dump[$i] . $dump[$i+1];

        if( $ches === "00" )
        {
            $dump[$i+1] = "A";
        }

        $i += 2;
    }

    return $dump;
}

?>
