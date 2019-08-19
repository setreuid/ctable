<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require_once('lib/util.php' );

header("Access-Control-Allow-Origin : *");
header("Access-Control-Allow-Headers : Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json");

$PARAMS = json_decode(file_get_contents('php://input'));

if (!isset($PARAMS->userid))
{
    echo json_encode(array('result' => 'error', 'message' => '학번을 입력하지 않았습니다'));
    exit(1);
}

//////////////////////////////////////////////////////////////////////////////////////
// 시간표 받아옴
//////////////////////////////////////////////////////////////////////////////////////


// http://m.chosun.ac.kr/mbs/mobile2/jsp/bd/haksa_03.jsp?id=mobile2_070300000000
// http://m.chosun.ac.kr/mbs/mobile2/jsp/bd/bdLoginOK.jsp

$agent = 'Mozilla/5.0(Linux; U; Android 2.2; en-gb; LG-P500 Build/FRF91) AppleWebKit/533.0 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1';
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,"https://m.chosun.ac.kr/mbs/mobile2/jsp/bd/bdLoginOK.jsp");
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt ($ch, CURLOPT_REFERER, "http://m.chosun.ac.kr/mbs/mobile2/jsp/bd/bdLogin.jsp");
curl_setopt ($ch, CURLOPT_SSLVERSION, 1);
curl_setopt ($ch, CURLOPT_HEADER, 0);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, "id=" . $PARAMS->userid . "&pw=" . $PARAMS->userpw . "&cls=01&preUrl=http://m.chosun.ac.kr:80/mbs/mobile2/jsp/bd/bdLogin.jsp"); // Post 값 Get 방식처럼적는다.
curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/user_cookies/' . $PARAMS->userid . '.txt');
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, "RC4-MD5");
curl_setopt ($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Host: m.chosun.ac.kr',
    'Origin: http://m.chosun.ac.kr',
    'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
    'Referer: http://m.chosun.ac.kr/mbs/mobile2/jsp/bd/bdLogin.jsp',
    'Accept-Encoding: gzip, deflate, br',
    'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7'
));
$buffer = curl_exec ($ch);
$cinfo = curl_getinfo($ch);
curl_close($ch);
if ($cinfo['http_code'] != 200)
{
    echo json_encode( array( 'result' => 'error', 'message' => 'Server Error : 1-' . $cinfo['http_code'] ) );
    exit(1);
}
// echo $buffer;

// smt_cd - 10:1학기 11:하계 20:2학기 21:동계

$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,"http://m.chosun.ac.kr/mbs/mobile2/jsp/bd/haksa_03.jsp?id=mobile2_070300000000"); //접속할 URL 주소
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, "smt_cd=" . $PARAMS->class );
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt ($ch, CURLOPT_SSLVERSION,3);
curl_setopt ($ch, CURLOPT_HEADER, 0);
curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/user_cookies/' . $PARAMS->userid . '.txt');
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, "RC4-MD5");
curl_setopt ($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Host: m.chosun.ac.kr',
    'Origin: http://m.chosun.ac.kr',
    'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
    'Referer: http://m.chosun.ac.kr/mbs/mobile2/jsp/bd/bdLogin.jsp',
    'Accept-Encoding: gzip, deflate, br',
    'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7'
));
$buffer = curl_exec($ch);
$cinfo = curl_getinfo($ch);
curl_close($ch);
if ($cinfo['http_code'] != 200)
{
	echo json_encode(array('result' => 'error', 'message' => 'Server Error : 2-' . $cinfo['http_code']));
    exit(1);
}
// echo $buffer;

if (strrpos($buffer, "아이디를 입력하십시오") !== false)
{
    echo json_encode(array('result' => 'error', 'message' => '학번을 틀렸거나 패스워드에 오타가 있어요!'));
    exit(1);
}

$st_pos = strpos( $buffer, 'tbody' );
$ed_pos = strpos( $buffer, '/tbody', $st_pos );
$length = $ed_pos - $st_pos;

$buffer = substr( $buffer, $st_pos, $length );
// exit(1);

//////////////////////////////////////////////////////////////////////////////////////
// 데이터 가공
//////////////////////////////////////////////////////////////////////////////////////

preg_match_all ( "|<a class=\"r_pop_open\".+</a>|U", $buffer, $result );
$map = $result[0];

$final_result = array();
$i = 0;

foreach( $map as $item )
{
    preg_match( '|subject="(?<subject>.*)" curi_num="(?<curi_num>.*)" emp_nm="(?<emp_nm>.*)" room_nm="(?<room_nm>.*)" std_belong_nm="(?<std_belong_nm>.*)|U', $item, $matches );
    array_push( $final_result, array( "idx" => $i, "subject" => $matches['subject'], "curi_num" => $matches['curi_num'], "emp_nm" => $matches['emp_nm'], "room_nm" => $matches['room_nm'], "std_belong_nm" => $matches['std_belong_nm'] ) );
    $i++;
}

echo json_encode( array( 'result' => 'success', 'message' => 'OK', 'data' => $final_result ) );

?>
