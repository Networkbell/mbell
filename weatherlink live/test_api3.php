<?php

/****************************************
Retourne le station id
 ****************************************/

/*

ssb0klh1zv5dmcnqebtsucqdh8ovumwd
v14fosgef9vj9wiho3akattsiuneceu8

dyqtrpjgglfy1x3nsqpvitiretmapdkx
nnqnyozxk5xtqtqi3hty0lkbicvijgtj
*/

$APIkey = "dyqtrpjgglfy1x3nsqpvitiretmapdkx";
$APIsecret = "nnqnyozxk5xtqtqi3hty0lkbicvijgtj";


function getStationURL($livekey, $livesecret)
{

    $parameters = array(
        "api-key" => $livekey,
        "api-secret" => $livesecret,
        "t" => time()
    );


    ksort($parameters);

    $apiSecret = $parameters["api-secret"];
    unset($parameters["api-secret"]);

    $data = "";
    foreach ($parameters as $key => $value) {
        $data = $data . $key . $value;
    }

    $apiSignature = hash_hmac("sha256", $data, $apiSecret);

    $url = "https://api.weatherlink.com/v2/stations/?api-key=" . $parameters["api-key"] . "&api-signature=" . $apiSignature . "&t=" . $parameters["t"];
    return $url;
}


function getLiveStationAPI($key, $secret)
{

    $data = file_get_contents(getStationURL($key, $secret));
    $json = json_decode($data, true);
    return $json;
}


$datas = getLiveStationAPI($APIkey, $APIsecret);
$zero = '&#8709;';
$stat_id = isset($datas['stations'][0]['station_id']) ? $datas['stations'][0]['station_id'] : $zero;


print_r($stat_id);
