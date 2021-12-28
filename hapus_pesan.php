<?php
include_once("configuration.inc");
$activity[0]="messaging";
$activity[1]="contacts_main";
include_once("header.php");
include_once("includes/koneksi.php");
include_once("includes/function.php");

$id = get("id");

$q = mysqli_query($koneksi, "DELETE FROM pesan WHERE id='$id'");
redirect("kirim.php");
?>
