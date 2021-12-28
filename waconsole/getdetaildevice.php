<?php
header('Content-Type: application/json');
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");

$cURLConnection = curl_init();

curl_setopt($cURLConnection, CURLOPT_URL, url_wa() . "/getdetaildevice");
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$resp = curl_exec($cURLConnection);
curl_close($cURLConnection);

$data = json_decode($resp);
echo json_encode($data);
