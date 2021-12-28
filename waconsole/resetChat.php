<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");
$nomor = no_wa();
syncMSG($nomor . "@c.us");
$url = callback_wa() . "waconsole";
header("Location :$url");
