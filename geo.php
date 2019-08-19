<?php

header( "Access-Control-Allow-Origin : *" );
header( "Access-Control-Allow-Headers : Origin, X-Requested-With, Content-Type, Accept" );
header( "Content-Type: application/json" );
header( "Content-Type: text/html; charset=UTF-8" );

$addr = isset( $_REQUEST['ip'] ) ? $_REQUEST['ip'] : $_SERVER["REMOTE_ADDR"];
$addr = $addr === '' ? $_SERVER["REMOTE_ADDR"] : $addr;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://context.skyhookwireless.com/accelerator/ip?version=2.0&ip=".$addr."&prettyPrint=true&key=API_KEY&user=eval"); //접속할 URL 주소
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Host: api.udp.cc'));
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
}
curl_close($curl);
$decoded = json_decode($curl_response);

$params = array(
    "encoding" => "utf-8",
    "coord" => "latlng",
    "output" => "json",
    "query" => $decoded->data->location->latitude.",".$decoded->data->location->longitude
);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://maps.googleapis.com/maps/api/geocode/json?sensor=false&latlng=".$decoded->data->location->latitude.",".$decoded->data->location->longitude."&key=API_KEY&language=ko"); //접속할 URL 주소
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
}
curl_close($curl);
$decoded_geo = json_decode($curl_response);

if( isset( $_REQUEST['dong'] ) && $_REQUEST['dong'] == '1' )
{
    echo my_json_encode( array( "latitude" => $decoded->data->location->latitude, "longitude" => $decoded->data->location->longitude, "address" => $decoded_geo->results[0]->address_components[2]->short_name . ' ' . $decoded_geo->results[0]->address_components[1]->short_name ) ); 
}
else
{
    echo my_json_encode( array( "latitude" => $decoded->data->location->latitude, "longitude" => $decoded->data->location->longitude, "address" => $decoded_geo->results[0]->formatted_address ) );
}

function my_json_encode($arr)
{
    array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
    return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

?>
