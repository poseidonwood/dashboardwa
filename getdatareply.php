<?php
include_once("configuration.inc");
include_once("includes/koneksi.php");
include_once("includes/function.php");

if (isset($_REQUEST['id'])) {
  $id = $_REQUEST['id'];
  $query = "SELECT * FROM autoreply where id ='$id'";
  $execq = mysqli_query($koneksi, $query);
  $fdata = mysqli_fetch_assoc($execq);
  if ($fdata['forward_destinations'] !== NULL or $fdata['forward_destinations'] == "") {
    $fwd_data = explode("¶", $fdata['forward_destinations']);
    $responsepesan1 = $fwd_data[0];
    if ($responsepesan1 !== "") {
      $destinations_arr = explode(",", $fwd_data[0]);
      foreach ($destinations_arr as $arr_destination) {
        $nomortarget[] = $arr_destination;
      }
      $responsepesan = $fwd_data[1];
    } else {
      $responsepesan = NULL;
      $nomortarget = NULL;
    }
  } else {
    $responsepesan = NULL;
    $nomortarget = NULL;
  }
  $data = array(
    'id' => $id,
    'keyword' => $fdata['keyword'],
    'response' => $fdata['response'],
    'case_sensitive' => $fdata['case_sensitive'],
    'responsepesan' => $responsepesan,
    'forward_destinations' => $nomortarget
  );
  echo json_encode($data);
} else {
  echo json_encode(array('status' => false, 'message' => 'Wrong Parameters'));
}
