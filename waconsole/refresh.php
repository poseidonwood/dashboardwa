<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");

$login = cekSession();
if ($login == 0) {
    redirect("../login.php");
}

$nomorwa = no_wa();
syncMSG($nomorwa);

// echo "success";
