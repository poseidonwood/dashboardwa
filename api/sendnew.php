<?php

include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");
//TODO(done): debug dikembalikan
$debug = 0;
if ($debug == 1) {
  $fh = fopen("testajax.log", 'a');
  $buff = print_r($data, 1);
  fwrite($fh, $buff . "\r\n");
}

//TODO(done) pakai _REQUEST (done)

if (!isset($_REQUEST['nomor']) && !isset($_REQUEST['msg'])) {
  $ret['status'] = false;
  $ret['msg'] = "Nomor atau pesan tidak boleh kosong";
  echo json_encode($ret, true);
  exit;
} else {
  $nomor = $_REQUEST['nomor'];
  $pesan = $_REQUEST['msg'];
  $sender = $_REQUEST['sender'];
}

$res = sendMSG($nomor, $pesan, $sender);
echo json_encode($res);

//TODO(done): buatkan function atau masukkan di sendMSG (done)


if ($debug == 1) {
  fwrite($fh, $res . "\r\n");
  fclose($fh);
}
