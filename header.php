<?php
//-------------------------------------------------------------------------------
//
//   This notice must remain untouched at all times.
//   Copyright Alucio Net Technologies 2006. All rights reserved.
//   By Alucio Net Technologies.  Last modified 03-07-2006.
//
//   This script/code is owned and copyrighted by Alucio Net Technologies.
//   The following code cannot be duplicated, distributed, or modified
//   without prior agreements from Alucio Net Technologies. Violations to
//   this terms will be considered as criminal acts and the violators will
//   be persecuted to the maximum possible extent by the governing laws.
//
//   Author           : Junaidi Halin
//   Date created     : 06/13/06
//
//   Modifications history:
//      - 06/13/06 JHALIN Created script
//      - 11/25/06 Lenny edit for group entity
//-------------------------------------------------------------------------------
?>
<?php
require_once("configuration.inc");

if ( (!isset($ajax_call)) || 
     ((isset($ajax_call)) && ($ajax_call!=1)) )
{
//require_once(COMMON_INC_DIR."gui.api.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>

<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<link href="https://synergis.alucio.net.id/css/font-awesome/css/all.css" rel="stylesheet" type="text/css" />
<script src="https://synergis.alucio.net.id/js/jquery.min.js"></script>
<script src="https://synergis.alucio.net.id/js/popper.min.js"></script>
</HEAD>
<?php
}
?>
