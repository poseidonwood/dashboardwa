<?php
include ("function.php");
// $data = array();
$data = getPesan("status = 'MENUNGGU JADWAL'");
$getTime = date('Y-m-d H:i');
// $getTime = "2021-12-28 14:39";
// echo $getTime;
$datanotif = array();
if(is_array($data)){
    foreach ($data as $datalist){
    $datanya[] = $datalist;
    $time = date('Y-m-d H:i',strtotime($datalist['jadwal']));
    
    if($time == $getTime){
        $data = sendMSG($datalist['nomor'], $datalist['pesan'], "CRONJOB");
        // $data = json_decode($send,true);
        if(is_array($data)){
            if($data['status'] == true){
                updateStatusMSG($datalist['id'],"TERKIRIM");
                $datanotif[] = json_encode($data);
            }
        }
    }
    // else{
    //     echo "$time $getTime";
    // echo "here1";
    // }
  }
}

echo json_encode($datanotif);
?>