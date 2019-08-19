<meta charset="utf-8"/>
<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

header( "Access-Control-Allow-Origin : *" );
header( "Access-Control-Allow-Headers : Origin, X-Requested-With, Content-Type, Accept" );
// header( "Content-Type: application/json" );

$agent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Accept: application/vnd.twitchtv.v3+json';

$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,"https://api.twitch.tv/kraken/streams/setreuid"); //접속할 URL 주소
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 인증서 체크같은데 true 시 안되는 경우가 많다.
curl_setopt ($ch, CURLOPT_SSLVERSION, 1); // SSL 버젼 (https 접속시에 필요)
curl_setopt ($ch, CURLOPT_HEADER, 0); // 헤더 출력 여부
curl_setopt ($ch, CURLOPT_POST, 0); // Post Get 접속 여부
curl_setopt ($ch, CURLOPT_TIMEOUT, 30); // TimeOut 값
curl_setopt ($ch, CURLOPT_REFERER, 'https://api.twitch.tv');
curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true); // 결과값을 받을것인지
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
$buffer = curl_exec ($ch);
$cinfo = curl_getinfo($ch);
curl_close($ch);

echo $buffer;

?>
