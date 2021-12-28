<?php


$username = "wafekusanew";
$password = "wafekusanew";
$db = "wafekusanew";
$koneksi = mysqli_connect("localhost", $username, $password, $db) or die("GAGAL");
mysqli_set_charset($koneksi, "utf8mb4");
$base_url = "https://fastwa.fekusa.xyz/messaging/";
date_default_timezone_set('Asia/Jakarta');
// $data = array(
//    'host' => $db_host,
//    'username' => $username,
//    'password' => $password,
//    'db' => $db,
//    'dbport' => $db_port,
// );
// echo json_encode($data);
