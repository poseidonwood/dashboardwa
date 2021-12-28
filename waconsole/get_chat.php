<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");
/*
$login = cekSession();
if($login == 0){
    redirect("../login.php");
}
*/
//$url_example = "https://messaging.fekusa.xyz/images/";
$url_example = "https://wa.fekusa.xyz/Image";

$debug = 0;

if ($debug == 1) {
   $fh = fopen("testajax.log", 'a');
   fwrite($fh, print_r($_REQUEST, 1));
}

function getProfile($jid)
{
   $profile_data = getContactProfile($jid);
   if ($profile_data['name'] != "")
      return $profile_data['name']; /// href to contact
   else
      return "+" . explode("@", $jid)[0];
}

function getQuotedMessage($msgID, $jid = "")
{
   global $koneksi;
   $query = mysqli_query($koneksi, "SELECT * FROM chat_messages " .
      "WHERE remoteJid LIKE '{$jid}%' AND msgID='{$msgID}' LIMIT 1");

   $row = mysqli_fetch_assoc($query);
   if (is_array($row)) {
      if ($row['type'] == "0")
         return $row['message'];
      elseif (($row['type'] == "1") || ($row['type'] == "2"))
         return "<audio controls><source src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.ogg' type='audio/ogg'></audio>" .
            "<br />{$row['message']}";
      elseif ($row['type'] == "3")
         return "{$row['message']} <img width=64 height=64 src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.jpeg' />";
      elseif ($row['type'] == "4")
         return "<video controls style='max-width:50%;'><source src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.mp4' type='video/mp4'></video>" .
            "<br />{$row['message']}";
      elseif ($row['type'] == "5")
         return "<a target=_new href='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.pdf'>{$row['message']}</a>";
      elseif ($row['type'] == "6")
         return "<img height=50% width=50% src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.webp' />";
      elseif ($row['type'] == "7")
         return "Lokasi";
      elseif ($row['type'] == "8")
         return "Kartu Nama";
   } else
      return "Pesan Acuan Tidak ditemukan";
}

if (isset($_REQUEST['nomor'])) {
   $nomor = $_REQUEST['nomor'];
   if (!preg_match("/@/", $nomor))
      $nomor .= "@";

   // if (strpos($nomor, "@g.us") !== false) {
   //    $query1 = "SELECT * FROM chat_messages WHERE remoteJid LIKE '{$nomor}%' ORDER BY messageTimestamp DESC";
   // } else {
   //    $query1 = "SELECT * FROM chat_messages WHERE remoteJid LIKE '{$nomor}%' ORDER BY messageTimestamp DESC limit 5";
   // }
   $query1 = "SELECT * FROM chat_messages WHERE remoteJid LIKE '{$nomor}%' ORDER BY messageTimestamp DESC limit 5";

   $limit = "";
   if (isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
      // if (strpos($nomor, "@g.us") !== false) {
      //    $query1 = "SELECT * FROM chat_messages WHERE remoteJid LIKE '{$nomor}%' ORDER BY messageTimestamp DESC";
      // } else {
      //    $query1 = "SELECT * FROM chat_messages WHERE remoteJid LIKE '{$nomor}%' ORDER BY messageTimestamp DESC limit $limit";
      // }
      $query1 = "SELECT * FROM chat_messages WHERE remoteJid LIKE '{$nomor}%' ORDER BY messageTimestamp DESC limit $limit";
   }
   $q = mysqli_query($koneksi, $query1);
   $final = [];
   while ($row = mysqli_fetch_assoc($q)) {
      $mid = $row['msgID'];
      // if (strpos($nomor, "@g.us") !== false) {
      //    $query2 = "SELECT *FROM blast where nomor ='$mid'";
      // } else {
      //    $query2 = "SELECT *FROM blast where nomor ='$mid' limit 5";
      // }
      $query2 = "SELECT *FROM blast where nomor ='$mid' limit 5";

      if (isset($_REQUEST['limit'])) {
         // if (strpos($nomor, "@g.us") !== false) {
         //    $query2 = "SELECT *FROM blast where nomor ='$mid'";
         // } else {
         //    $query2 = "SELECT *FROM blast where nomor ='$mid' limit $limit";
         // }
         $query2 = "SELECT *FROM blast where nomor ='$mid' limit $limit";
      }

      $querydata = mysqli_query($koneksi, $query2);
      if (mysqli_num_rows($querydata) > 0) {
         $fetchdata  = mysqli_fetch_assoc($querydata);
         $sender = $fetchdata['make_by'];
      } else {
         $sender = "UNKNOWN";
      }
      if ($row['fromMe']) {
         if ($row['ack'] == "-1") $row['ack'] = "E";
         elseif ($row['ack'] == "0") $row['ack'] = "✔️";
         elseif ($row['ack'] == "1") $row['ack'] = "✔️";
         elseif ($row['ack'] == "2") $row['ack'] = "✔️";
         elseif ($row['ack'] == "3") $row['ack'] = "✔️✔️";
         elseif ($row['ack'] == "4") $row['ack'] = "✔️✔️✔️";
         $row['ack'] = " {$row['ack']}";
      } else
         $row['ack'] = "";
      if (date("Y-m-d", $row['messageTimestamp']) == date("Y-m-d"))
         $row['tanggal'] = date("H:i", $row['messageTimestamp']);
      else
         $row['tanggal'] = date("d M y H:i", $row['messageTimestamp']);
      //$row['tanggal'].=$row['ack'];

      if ($row['type'] != "8") {
         $message = htmlspecialchars($row['message'], ENT_SUBSTITUTE);
         if ($row['fromMe'])
            $message .= "<hr><b><i>(Sent by $sender)</b></i>";
      } elseif ($row['type'] != "5") {
         $message = $row['message'];
         if ($row['fromMe'])
            $message .= "<hr><b><i>(Sent by $sender)</b></i>";
      }

      if (preg_match("/g\.us/", $row['remoteJid']) && !($row['fromMe'])) {
         $participant_info = getProfile(explode("|", $row['remoteJid'])[1]);
         $row['message'] = "<span style='color:blue;'>{$participant_info}</span><br />";
      } else
         $row['message'] = "";

      if ($row['isForwarded']) {
         $row['message'] .= "<svg width='16' height='16' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M9.519 3.875a.54.54 0 0 1 .922-.382l4.03 4.034a.54.54 0 0 1 0 .764l-4.03 4.034a.54.54 0 0 1-.922-.383v-1.821c-3.398 0-5.886.97-7.736 3.074-.164.186-.468.028-.402-.211.954-3.449 3.284-6.67 8.138-7.363V3.875z' fill='#923224'></path></svg> " .
            "<span style='color:#923224;font-style:italic;'>Pesan Terusan</span><br />";
      } elseif ($row['quotedMsgID'] != "") {
         $quotedMsgdata = explode(":", $row['quotedMsgID']);
         $row['message'] .= "<div style='background-color:#f2f8f2;color:#808080;'>";
         if (preg_match("/g\.us/", $row['remoteJid']) && !($row['fromMe']))
            $row['message'] .= getProfile($quotedMsgdata[0]) . "<br />";
         $row['message'] .= getQuotedMessage($quotedMsgdata[1], $quotedMsgdata[0]) .
            "</div><br />";
      }

      if ($row['type'] != "8")
         $row['message'] .= nl2br($message);

      if (($row['type'] == "1") || ($row['type'] == "2"))
         $row['message'] = "<audio controls><source src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.ogg' type='audio/ogg'></audio>" .
            "<br />{$row['message']}";
      elseif ($row['type'] == "3")
         $row['message'] = "<img src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.jpeg' /><br />{$row['message']}";
      elseif ($row['type'] == "4")
         $row['message'] .= "<video controls style='max-width:50%;'><source src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.mp4' type='video/mp4'></video>" .
            "<br />{$row['message']}";
      elseif ($row['type'] == "5")
         $row['message'] = "<a target=_new href='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.pdf'>{$row['message']}</a>";
      //$row['message'].="<br />Dokumen";
      elseif ($row['type'] == "6")
         $row['message'] = "<img height=50% width=50% src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.webp' />";
      elseif ($row['type'] == "7") {
         $location_data = json_decode(htmlspecialchars($row['message'], ENT_SUBSTITUTE));
         $row['message'] = "<a target=_new href='https://www.google.com/maps/search/?api=1&query=" .
            "{$location_data->latitude},{$location_data->longitude}'>" .
            "<img src='" . $url_example .
            explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.jpeg' /></a>";
         if (isset($location_data->description) && ($location_data->description != ""))
            $row['message'] .= "<br />Deskripsi: {$location_data->description}";
      } elseif ($row['type'] == "8") {
         require_once('vcf.php');

         $vCard = new vCard(
            false, // Path to vCard file
            $message, // Raw vCard text, can be used instead of a file
            array( // Option array
               // This lets you get single values for elements that could contain multiple values but have only one value.
               //      This defaults to false so every value that could have multiple values is returned as array.
               'Collapse' => false
            )
         );
         $row['message'] .= "<b>Kartu Nama</b><br />" .
            vcf_to_string($vCard) . "<br />";
         unset($vCard);
      }
      /*
// if ($row['msgID']=='5BBBB5FEF287FFAD7F73DC7CD83229BD')
//    print_r($row);

*/
      $final[] = $row;
   }
   //print_r($final);
   // var_dump($final);
   echo json_encode($final);
} else if (isset($_REQUEST['msgID'])) {
   $msgID = $_REQUEST['msgID'];
   $imgQuery = mysqli_query($koneksi, "SELECT * FROM chat_messages where msgID='{$msgID}' LIMIT 1");

   $row = mysqli_fetch_assoc($imgQuery);
   $geturlimage =  $url_example . "whatsapp/" .
      explode("@", $row['remoteJid'])[0] . "-{$row['msgID']}.jpeg";
   echo "<img src='$geturlimage' width ='50%'>";
}

if ($debug == 1) fclose($fh);
