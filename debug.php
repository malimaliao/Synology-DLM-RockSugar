<?php

include_once "./search.php";

$RockSugar= new RockSugarSearch();
$RockSugar -> debug = true;

$curl_http = curl_init();
$RockSugar -> prepare($curl_http,'热辣滚烫');

$res = curl_exec($curl_http);
if (curl_errno($curl_http)) {
    $res_101 =  curl_error($curl_http);
}
curl_close($curl_http);


$RockSugar -> parse('DS',$res);
