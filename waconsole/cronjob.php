<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");

//Get data PROFILE
$query = "SELECT * FROM profiles where profile_picture_path != '' AND profile_picture_path != 'undefined'";
$exeq = mysqli_query($koneksi, $query);
foreach ($exeq as $data) {
  // var_dump($data);
  $id = $data['remoteJid'];
  $profilepic = $data['profile_picture_path'];
  if (strpos($profilepic, 'whatsapp') !== false) {
    $getvalidurl = getdatafromurl($profilepic);
    if ($getvalidurl == "URL signature expired") {
      $queryu = "UPDATE profiles set profile_picture_path = '' where remoteJid = '$id'";
      mysqli_query($koneksi, $queryu);
    } else {
      // echo "<img src='../img/profile/$profilepic' width ='200px'><br>";
      // Initialize the cURL session
      $ch = curl_init($profilepic);

      // Initialize directory name where
      // file will be save
      $dir = '../../messaging/img/profile/';

      // Use basename() function to return
      // the base name of file
      // $file_name = basename($profilepic);
      $file_name = $id . ".jpeg";
      // Save file into file location
      $save_file_loc = $dir . $file_name;

      // Open file
      $fp = fopen($save_file_loc, 'wb');

      // It set an option for a cURL transfer
      curl_setopt($ch, CURLOPT_FILE, $fp);
      curl_setopt($ch, CURLOPT_HEADER, 0);

      // Perform a cURL session
      curl_exec($ch);

      // Closes a cURL session and frees all resources
      curl_close($ch);

      // Close file
      fclose($fp);
      $queryu = "UPDATE profiles set profile_picture_path = '$file_name' where remoteJid = '$id'";
      mysqli_query($koneksi, $queryu);
    }
    $status = true;
  } else {
    $status = false;
  }
  if ($status !== false) {
    $data1[] = array(
      'id' => $id,
      'status' => $status
    );
  } else {
    $data1 = NULL;
  }
}

echo json_encode($data1 !== NULL ? $data1 : array('message' => 'No Action'));
