<?php

function geoInfomation()
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://context.skyhookwireless.com/accelerator/ip?version=2.0&ip=".$_SERVER["REMOTE_ADDR"]."&prettyPrint=true&key=API_KEY&user=eval");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, "RC4-MD5");
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
    curl_setopt($curl, CURLOPT_URL, "https://maps.googleapis.com/maps/api/geocode/json?sensor=false&latlng=".$decoded->data->location->latitude.",".$decoded->data->location->longitude."&key=API_KEY&language=ko");
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

    return array( "latitude" => $decoded->data->location->latitude, "longitude" => $decoded->data->location->longitude, "address" => $decoded_geo->results[0]->formatted_address );
}

?>
