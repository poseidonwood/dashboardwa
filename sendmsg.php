<?php
include_once("configuration.inc");
require_once "includes/class.whatsapp_messaging.php";
include_once("includes/koneksi.php");
include_once("includes/function.php");

function getWAqueue()
{
   $mComm=new cMessagingWA();
   return $mComm->getUnsentMessages(); 
}

function update_message_sent($msg_id,$status)
{
   $mComm=new cMessagingWA();
   return $mComm->update_message_sent($msg_id,$status); 
}

$debug=0;
$WAq="";
$WAq=getWAqueue();
if (is_array($WAq))
{
   foreach ($WAq as $data)
   {
//   print_r($data);
      $nomor = $data['nomor'];
      $pesan = $data['pesan'];
      header('Content-Type: application/json');

      if (!isset($nomor) && !isset($pesan))
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
            update_message_sent($data['id'],"TERKIRIM");
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
   sleep(2);
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
