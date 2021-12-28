<?php
include_once("koneksi.php");
session_start();

function get($param)
{
    global $koneksi;
    $d = isset($_GET[$param]) ? $_GET[$param] : null;
    $d = mysqli_real_escape_string($koneksi, $d);
    $d = filter_var($d, FILTER_SANITIZE_STRING);
    return $d;
}

function post($param)
{
    global $koneksi;
    $d = isset($_POST[$param]) ? $_POST[$param] : null;
    $d = mysqli_real_escape_string($koneksi, $d);
    $d = filter_var($d, FILTER_SANITIZE_STRING);
    return $d;
}

function login($u, $p)
{
    global $koneksi;
    $p = sha1($p);
    $q = mysqli_query($koneksi, "SELECT * FROM account WHERE username='$u' AND password='$p'");
    if (mysqli_num_rows($q)) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $u;
        $_SESSION['level'] = getSingleValDB("account", "username", $u, "level");
        return true;
    } else {
        return false;
    }
}

function cekSession()
{
    $login = isset($_SESSION['login']) ? $_SESSION['login'] : null;
    if ($login == true) {
        return 1;
    } else {
        return 0;
    }
}

function getSingleValDB($table, $where, $param, $target)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT * FROM `$table` WHERE `$where`='$param'");
    $row = mysqli_fetch_assoc($q);
    return $row[$target];
}
function getPesan($condition = NULL)
{
    global $koneksi;
    if($condition !== NULL){
        $condition = "WHERE ".$condition;
    }
    $q = mysqli_query($koneksi, "SELECT * FROM `pesan` $condition ORDER BY `time` DESC");
    while($row = mysqli_fetch_assoc($q)){
        $data[] = $row;
    }
    return $data;
}

function countDB($table, $where = null, $param = null)
{
    global $koneksi;
    if ($where == null && $param == null) {
        $query = "SELECT * FROM `$table`";
        $q = mysqli_query($koneksi, $query);
    } else {
        $query = "SELECT * FROM `$table` WHERE `$where`='$param'";
        $q = mysqli_query($koneksi, $query);
    }

    $row = mysqli_num_rows($q);
    return $row;
}
function saveData($query = null)
{
    global $koneksi;
    mysqli_query($koneksi, $query) or die("Error: $query " . mysqli_error($koneksi));
}
//TODO(done): ganti menjadi fungsi yg spesifik
function validasiPass($param1, $param2)
{
    global $koneksi;
    $query = "SELECT * FROM `account` WHERE `id`='$param1' AND `password` = '$param2'";
    $q = mysqli_query($koneksi, $query);
    $row = mysqli_num_rows($q);
    if ($row == 0) {
        return false;
    } else {
        return true;
    }
}

function countPresentase()
{
    $a = countDB("pesan", "status", "TERKIRIM");
    $b = countDB("pesan");
    if ($a > 0) {
        return (countDB("pesan", "status", "TERKIRIM") / countDB("pesan")) * 100;
    } else {
        return 0;
    }
}

function getAllNumber()
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT * FROM `profiles`");
    $arr = [];
    while ($row = mysqli_fetch_assoc($q)) {
        $number = str_replace("@c.us", "", $row['remoteJid']);
        array_push($arr, $number);
    }
    return $arr;
}

function getNama($nomor = "")
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT * FROM `profiles` where remoteJid like '%" . $nomor . "%'");
    $row = mysqli_fetch_assoc($q);
    return $row['name'];
}

function getLastID($table)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT * FROM `$table` ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_assoc($q);
    return $row['id'];
}

function url_wa()
{
    return getSingleValDB("pengaturan", "id", "1", "wa_gateway_url");
}
function callback_wa()
{
    return getSingleValDB("pengaturan", "id", "1", "callback");
}
function no_wa()
{
    return getSingleValDB("pengaturan", "id", "1", "nomor");
}
function api_key()
{
    return getSingleValDB("pengaturan", "id", "1", "api_key");
}

function redirect($target)
{
    echo '
    <script>
    window.location = "' . $target . '";
    </script>
    ';
    exit;
}

function toastr_set($status, $msg)
{
    $_SESSION['toastr'] = true;
    $_SESSION['toastr_status'] = $status;
    $_SESSION['toastr_msg'] = $msg;
}

function toastr_show()
{
    $t = isset($_SESSION['toastr']) ? $_SESSION['toastr'] : null;
    $t_s = isset($_SESSION['toastr_status']) ? $_SESSION['toastr_status'] : null;
    $t_m = isset($_SESSION['toastr_msg']) ? $_SESSION['toastr_msg'] : null;
    if ($t == true) {
        if ($t_s == "success") {
            echo "
            toastr.success('Sukses', '" . $t_m . "');
            ";
        }

        if ($t_s == "error") {
            echo "
            toastr.error('Kesalahan', '" . $t_m . "');
            ";
        }

        unset($_SESSION['toastr']);
        unset($_SESSION['toastr_status']);
        unset($_SESSION['toastr_msg']);
    }
}

function clearChat($number)
{
    global $koneksi;
    $url = url_wa() . "/clearchat";
    $data = [
        "number" => $number
    ];
    $data = json_encode($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    $response = curl_exec($curl);
    $query = "DELETE FROM chat_messages WHERE remoteJid LIKE '{$number}@%'";
    $q = mysqli_query($koneksi, $query);
    return json_decode($response, true);
}

function getChatMessages($number, $latestOnly = 1)
{
    global $koneksi;
    $url = url_wa() . "/getChat";
    $data = [
        "number" => $number,
        // "latestOnly" => $latestOnly
    ];
    $data = json_encode($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    $response = curl_exec($curl);

    return json_decode($response, true);
}

function syncContacts()
{
    global $koneksi;
    $url = url_wa() . "/syncContacts";
    $data = [
        "key" => "1234"
    ];
    $data = json_encode($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    $response = curl_exec($curl);

    return json_decode($response, true);
}

function sendMSG($number, $msg, $sender)
{
    $url = url_wa() . "/send";
    $date = date('Y-m-d H:i:s');
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "number=$number&message=$msg",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $res =  json_decode($response, true);
    $idmessage = $res['data']['response']['id']['id'];
    // var_dump($nomor);
    if ($res['status'] == "true") {
        $ret['status'] = true;
        $ret['msg'] = "Pesan berhasil dikirim";
        $ret['sender'] = $sender;
    } else {
        $ret['status'] = false;
        $ret['msg'] = $res['msg'];
        $ret['sender'] = NULL;
    }
    // $query = "INSERT INTO `blast`  (`id`, `nomor`, `pesan`, `media`, `jadwal`, `make_by`) VALUES ('NULL', '$idmessage', '$msg', NULL, '$date', '$sender')";
    // saveData($query);

    return $res;
}
function sendMedia($nomor = null, $caption = null, $file = null, $username = null)
{
    $url = url_wa() . "/send-media";
    $date = date('Y-m-d H:i:s');
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "number=$nomor&caption=$caption&file=$file",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $res =  json_decode($response, true);
    $idmessage = $res['response']['id']['id'];
    // var_dump($nomor);
    if ($res['status'] == "true") {
        $ret['status'] = true;
        $ret['msg'] = "Pesan berhasil dikirim";
        $ret['sender'] = $username;
    } else {
        $ret['status'] = false;
        $ret['msg'] = $res['msg'];
        $ret['sender'] = NULL;
    }
    $query = "INSERT INTO `blast`  (`id`, `nomor`, `pesan`, `media`, `jadwal`, `make_by`) VALUES ('NULL', '$idmessage', '$caption', '$file', '$date', '$username')";
    saveData($query);

    return $res;
}


function cekStatusWA()
{
    global $base_url;
    $url = $base_url . "status.php";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    //curl_setopt($curl, CURLOPT_POST, 1);
    //curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    $response = curl_exec($curl);
    // return $url;
    return json_decode($response, true);
}

function updateStatusMSG($id, $a)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "UPDATE pesan SET status='$a' WHERE id='$id'");
}

function base64upload($base64_string, $output_file)
{
    $file = fopen($output_file, "wb");

    $data = explode(',', $base64_string);

    fwrite($file, base64_decode($data[1]));
    fclose($file);

    return $output_file;
}

function phoneToStandard($nomor)
{
    $nomor = explode("@", $nomor)[0];
    $nomor = substr($nomor, 2);
    $nomor = "0" . $nomor;

    return $nomor;
}

function sendApiUrl()
{
    global $base_url;
    // $base_url = "http://localhost/aluciodev/messaging/";
    //return $base_url."api/send.php?key=".getSingleValDB("pengaturan", "id", "1", "api_key");
    // return $base_url . "api/send.php";
    return $base_url . "api/sendnew.php";
}

//TODO(done): Hapus syncMSG Lama kalau sdh tidak perlu

//TODO(done): Ganti nama
function syncMSG($nomor = null)
{
    global $koneksi;
    $url = url_wa() . "/getChat";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "number=$nomor",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}

function getContacts($limit = null)
{
    global $koneksi;
    if ($limit == null) {
        //$q = mysqli_query($koneksi, "SELECT remoteJid, MAX(messageTimestamp) FROM messages GROUP BY remoteJid ORDER BY messageTimestamp DESC");
        $q = mysqli_query($koneksi, "SELECT SUBSTRING_INDEX(remoteJid, '|', 1) rJid, MAX(messageTimestamp) maxtime 
                                 FROM chat_messages GROUP BY rJid ORDER BY maxtime DESC");
    } else {
        $q = mysqli_query($koneksi, "SELECT SUBSTRING_INDEX(remoteJid, '|', 1) rJid, MAX(messageTimestamp) maxtime 
        FROM chat_messages GROUP BY rJid ORDER BY maxtime DESC limit $limit");
    }

    return $q;
}

function getContactProfile($jid)
{
    global $koneksi;
    $query = "SELECT name,profile_picture_path FROM profiles WHERE remoteJid='{$jid}'";
    $q = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($q);
    return $row;
}

function getLastMsg($nomor)
{
    global $koneksi;

    $q = mysqli_query($koneksi, "SELECT type,message, messageTimestamp FROM chat_messages WHERE remoteJid LIKE '{$nomor}%' ORDER BY messageTimestamp DESC LIMIT 1");
    $row = mysqli_fetch_assoc($q);
    if (date("Y-m-d", $row['messageTimestamp']) == date("Y-m-d"))
        $row['messageTimestamp'] = date("H:i", $row['messageTimestamp']);
    else
        $row['messageTimestamp'] = date("d M y H:i", $row['messageTimestamp']);

    //$message=json_decode($row['message']);
    if (($row['type'] == "1") || ($row['type'] == "2"))
        $row['message'] = "Audio";
    elseif ($row['type'] == "3")
        $row['message'] = "Gambar";
    elseif ($row['type'] == "4")
        $row['message'] = "Video";
    elseif ($row['type'] == "5")
        $row['message'] = "Dokumen";
    elseif ($row['type'] == "6")
        $row['message'] = "Sticker";
    elseif ($row['type'] == "7")
        $row['message'] = "Lokasi";
    elseif ($row['type'] == "8")
        $row['message'] = "Kartu Nama";

    return $row;
}

function checkExist($table, $where, $param)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT * FROM `$table` WHERE `$where`='$param' LIMIT 1");
    $row = mysqli_num_rows($q);
    if ($row == 0) {
        return false;
    } else {
        return true;
    }
}

function callback($id_pesan, $nomor, $pesan, $tanggal, $nomor_saya)
{
    $url = getSingleValDB("pengaturan", "id", "1", "callback");

    if ($url != null) {
        $data = [
            "id_pesan" => $id_pesan,
            "nomor" => $nomor,
            "pesan" => $pesan,
            "tanggal" => $tanggal,
            "nomor_saya" => $nomor_saya
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        return json_decode($response, true);
    }
}

function cekNomorWhatsapp($number)
{
    $url = url_wa() . "/cek-nomor";
    $data = [
        "number" => $number
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    $r = json_decode($response, true);
    return $r['status'];
}

function autoReply($nomor, $pesan)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT * FROM autoreply");
    while ($row = mysqli_fetch_assoc($q)) {
        if ($row['case_sensitive'] == "0") {
            //NON SENSITIF
            $pesan = strtolower($pesan);
            if (strpos($pesan, $row['keyword']) !== false) {
                sendMSG($nomor, $row['response']);
                return true;
                break;
            }
        } else {
            //SENSITIF
            if ($pesan == $row['keyword']) {
                sendMSG($nomor, $row['response']);
                return true;
                break;
            }
        }
    }
}
//TODO(done): Hapus google form kalau tdk perlu

function SpinText($string)
{
    $total = substr_count($string, "{");
    if ($total > 0) {
        for ($i = 0; $i < $total; $i++) {
            $awal = strpos($string, "{");
            $startCharCount = strpos($string, "{") + 1;
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, "}");
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            $hasil1 =  substr($firstSubStr, 0, $endCharCount);
            $rw = explode("|", $hasil1);
            $hasil2 = $hasil1;
            if (count($rw) > 0) {
                $n = rand(0, count($rw) - 1);
                $hasil2 = $rw[$n];
            }
            $string = str_replace("{" . $hasil1 . "}", $hasil2, $string);
        }
        return $string;
    } else {
        return $string;
    }
}
function getdatafromurl($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}
function validateUrl($url = null)
{
    $headers = @get_headers($url);
    $httpStatus = intval(substr($headers[0], 9, 3));
    if ($httpStatus < 400) {
        return true;
    }
    return false;
}
