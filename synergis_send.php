<?php
include_once("configuration.inc");
require_once COMMON_INC_DIR.'class.messaging.php';
include_once("includes/koneksi.php");
include_once("includes/function.php");

function getWAqueue()
{
   $mComm=new cMessaging();
   return $mComm->getUnsentMessages("WA"); 
}

function update_message_sent($msg_id)
{
   $mComm=new cMessaging();
   return $mComm->update_message_sent($msg_id); 
}

$debug=0;
$WAq="";
$WAq=getWAqueue();
if (is_array($WAq))
{
   foreach ($WAq as $data)
   {
//   print_r($data);
      $nomor = $data['recipient_addrs']."@c.us";
      $pesan = $data['message'];
      header('Content-Type: application/json');

      if(!isset($nomor) && !isset($pesan))
      {
         $ret['status'] = false;
         $ret['msg'] = "Nomor / pesan tidak boleh kosong";
         echo json_encode($ret, true);
      }
      else
      {
         $res = sendMSG($nomor, $pesan);
         if ($res['status'] == "true")
         {
            $ret['status'] = true;
            $ret['msg'] = "Pesan berhasil dikirim";
            update_message_sent($data['msg_id']);
         }
         else
         {
            $ret['status'] = false;
            $ret['msg'] = $res['msg'];
         }

         $jsonresp=json_encode($ret, true);
         echo $jsonresp;
      }
   }
}

if ($debug==1)
{
   $json_data=json_encode($WAq);
   $fh=fopen("./WAq.json",'w+');
   fwrite($fh,$json_data);
   fwrite($fh,$jsonresp."\r\n");
   fclose($fh);
}
?>
