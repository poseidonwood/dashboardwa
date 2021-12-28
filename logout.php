<?php
include_once("../common/configuration.inc");
include_once("includes/koneksi.php");
include_once("includes/function.php");

session_destroy();
redirect("login.php");
