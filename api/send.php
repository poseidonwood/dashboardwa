<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");

$debug = 0;

// Takes raw data from the request
$data = json_decode(file_get_contents('php://input'), true);
//$data = $_REQUEST;

if ($debug == 1) {
    $fh = fopen("testajax.log", 'a');
    $buff = print_r($data, 1);
    fwrite($fh, $buff . "\r\n");
}

$nomor = $data['nomor'];
$pesan = $data['msg'];
header('Content-Type: application/json');

/*
$api_key = get("key");
if($api_key != api_key()){
    $ret['status'] = false;
    $ret['msg'] = "Api key salah";
    echo json_encode($ret, true);
    exit;
}
*/
if (!isset($nomor) && !isset($pesan)) {
    $ret['status'] = false;
    $ret['msg'] = "Nomor / pesan tidak boleh kosong";
    echo json_encode($ret, true);
    exit;
}

$res = sendMSG($nomor, $pesan);
if ($res['status'] == "true") {
    $ret['status'] = true;
    $ret['msg'] = "Pesan berhasil dikirim";
} else {
    $ret['status'] = false;
    $ret['msg'] = $res['msg'];
}

$jsonresp = json_encode($ret, true);
echo $jsonresp;

if ($debug == 1) {
    fwrite($fh, $jsonresp . "\r\n");
    fclose($fh);
}
