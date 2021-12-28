<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");

$debug = 0;
if ($debug == 1) {
  $fh = fopen("testajax.log", 'a');
  $buff = print_r($data, 1);
  fwrite($fh, $buff . "\r\n");
}

if (!isset($_REQUEST['nomor']) && !isset($_REQUEST['msg'])) {
  $ret['status'] = false;
  $ret['msg'] = "Nomor atau pesan tidak boleh kosong";
  echo json_encode($ret, true);
  exit;
} else {
  $nomor = $_REQUEST['nomor'];
  $pesan = strtolower($_REQUEST['msg']);
  $namecontact = $_REQUEST['namecontact'];
  if ($_SESSION['username'] == NULL or $_SESSION['username'] == "") {
    $sender = "AUTO REPLY";
  } else {
    $sender = $_SESSION['username'];
  }
}
// Cek Response 

// TODO: Pakai style synergis...pakai olahDB, panggil2 query pakai prosedur QueryDB

// $q = "SELECT * FROM autoreply where keyword like ''$pesan%' limit 1";
$q = "SELECT * FROM autoreply where keyword = '$pesan' limit 1";

$qexec = mysqli_query($koneksi, $q);
$nums = mysqli_num_rows($qexec);
if ($nums > 0) {
  $fq = mysqli_fetch_assoc($qexec);
  if ($fq['forward_destinations'] !== NULL) {
    // TODO: Get FWD message: 
    $fwd_data = explode("¶", $fq['forward_destinations']);
    $responsepesan = $fwd_data[1];
    $responsepesantosales = $fq['response'];
    $res_customer = sendMSG($nomor, $responsepesantosales, $sender);
    // Get Number 
    $destinations_arr = explode(",", $fwd_data[0]);
    // $destinations_arr = explode(",", $fq['forward_destinations']);
    foreach ($destinations_arr as $arr_destinations) {
      //{nama_customer} {link_no} Telah menghubungi anda dgn pesan: {pesan_customer}
      $getnama_customer = str_replace("{nama_customer}", $namecontact, $responsepesan);
      $getlink_no = str_replace("{link_no}", "wa.me/$nomor", $getnama_customer);
      $getpesan_customer = str_replace("{pesan_customer}", $pesan, $getlink_no);
      $send_forward = sendMSG($arr_destinations, $getpesan_customer, $sender);
    }
  } else {
    $responsepesan = $fq['response'];
    $res = sendMSG($nomor, $responsepesan, $sender);
  }
  //TODO: check autoforward, bisa multi nomor (explode)(done)
  // deteksi nomor atau email
  // prio terakhir deteksi pemilik/nama nomor pengirim(done)
  //    $res = sendMSG($nomor_fwd, "[Nama dan nomor_pelanggan dgn wa.me] telah menghubungi anda dgn pesan: $body_pesan_pelanggan, $pelanggan);
  // email:
  //   register ke synergis message
  exit;
}

if ($debug == 1) {
  fwrite($fh, $res . "\r\n");
  fclose($fh);
}

echo json_encode($res);
