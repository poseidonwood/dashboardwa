<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");

$debug=0;

if ($debug==1)
{
   $fh=fopen("testajax.log",'a');
   fwrite($fh,"here"."\r\n");
}

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

$res = syncContacts();
if ($res['status'] == "true")
{
   $ret['status'] = true;
   $ret['msg'] = "Contacts berhasil didownload";
   echo json_encode($ret, true);
}
else
{
   $ret['status'] = false;
   $ret['msg'] = $res['msg'];
   echo json_encode($ret, true);
}

if ($debug==1)
{
   $buff=print_r($res,1);
   fwrite($fh,$buff."\r\n");
   fclose($fh);
}
