<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");

$debug=0;

// Takes raw data from the request
$data = json_decode(file_get_contents('php://input'), true);

if ($debug==1)
{
   $fh=fopen("testajax.log",'a');
   $buff=print_r($data,1);
   fwrite($fh,$buff."\r\n");
}

$nomor = $data['nomor'];
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
if (!isset($nomor))
{
    $ret['status'] = false;
    $ret['msg'] = "Nomor tidak boleh kosong";
    echo json_encode($ret, true);
    exit;
}

$res = getChatMessages($nomor,$data['latestOnly']);
if($res['status'] == "true"){
    $ret['status'] = true;
    $ret['msg'] = "Percakapan berhasil didownload";
    echo json_encode($ret, true);
}else{
    $ret['status'] = false;
    $ret['msg'] = $res['msg'];
    echo json_encode($ret, true);
}

if ($debug==1)
{
   fclose($fh);
}
