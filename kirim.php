<?php
include_once("configuration.inc");
$activity[0] = "messaging";
$activity[1] = "contacts_main";
include_once("header.php");
include_once("includes/koneksi.php");
include_once("includes/function.php");
$base_url1 = "http://127.0.0.1/aluciodev/messaging/";
if (!isset($user_session)) {
    $login = cekSession();
    if ($login == 0) {
        redirect("login.php");
    }
}

$active_menu = "sendwa";

if (post("pesan")) {
    $username = $_SESSION['username'];
    // $username = "admin";
    $pesan = post("pesan");
    $unsub = post("unsubscribe");
    $jadwal = date("Y-m-d H:i:s", strtotime(post("tgl") . " " . post("jam")));
    if (!empty($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['media']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['media']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));

        $allow = ['png', 'jpeg', 'pdf', 'jpg'];
        if (in_array($ext, $allow)) {
            if ($ext == "png") {
                $filename = round(microtime(true)) . mt_rand() . '.png';
            }

            if ($ext == "pdf") {
                $filename = round(microtime(true)) . mt_rand() . '.pdf';
            }

            if ($ext == "jpg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpg';
            }

            if ($ext == "jpeg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpeg';
            }
        } else {
            toastr_set("error", "Format png, jpg, pdf only");
            redirect("kirim.php");
            exit;
        }

        move_uploaded_file($_FILES['media']['tmp_name'], 'uploads/' . $filename);
        // Insert it into our tracking along with the original name
        $media = $base_url . "uploads/" . $filename;
    } else {
        $media = null;
    }

    if ($media == null) {
        $nomor = serialize(getAllNumber());
        $q = mysqli_query($koneksi, "INSERT INTO blast(`nomor`, `pesan`, `jadwal`, `make_by`)
        VALUES('$nomor', '$pesan', '$jadwal', '$username')");
    } else {
        $nomor = serialize(getAllNumber());
        $q = mysqli_query($koneksi, "INSERT INTO blast(`nomor`, `pesan`, `media`, `jadwal`, `make_by`)
        VALUES('$nomor', '$pesan', '$media', '$jadwal', '$username')");
    }
    if (isset($_POST['target'])) {
        $n = $_POST['target'];
    } else {
        $n = getAllNumber();
    }

    $id_blast = getLastID("blast");
    if (post("tiap_bulan") == "on") {
        $tiap_bulan = "1";
        $last_month = date("m", strtotime("-1 month"));
    } else {
        $tiap_bulan = "0";
        $last_month = date("m", strtotime("-1 month"));
    }

    foreach ($n as $nn) {
        $pesan = str_replace(['[NAMA]', '[nama]', '[Nama]'], getNama($nn), $pesan);
        $pesan = str_replace(['[NOMOR]', '[nomor]', '[Nomor]'], $nn, $pesan);
        if ($unsub) {
            $pesan = $pesan . "\r\nUntuk berhenti dari info ini balas *UNSUB* atau klik link berikut " . $base_url . "unsub.php?nomor=$nn";
        }
        // echo $pesan;
        $interval = rand(2, 7);
        $date = date_create($jadwal);
        $jadwal = date_format(date_add($date, date_interval_create_from_date_string("$interval seconds")), 'Y-m-d H:i:s');
        // echo $jadwal."<br>";
        if ($media == null) {
            $q = mysqli_query($koneksi, "INSERT INTO pesan(`id_blast`, `nomor`, `pesan`, `jadwal`, `tiap_bulan`, `last_month`, `make_by`)
            VALUES('$id_blast', '$nn', '" . SpinText($pesan) . "', '$jadwal', '$tiap_bulan', '$last_month', '$username')");
        } else {
            $q = mysqli_query($koneksi, "INSERT INTO pesan(`id_blast`, `nomor`, `pesan`, `media`, `jadwal`,`tiap_bulan`, `last_month`, `make_by`)
            VALUES('$id_blast', '$nn', '" . SpinText($pesan) . "', '$media', '$jadwal', '$tiap_bulan', '$last_month', '$username')");
        }
        sleep(0.2);
    }
    // exit;
    toastr_set("success", "Sukses kirim pesan terjadwal");
}
if (post("pesanlsg")) {
    $username = $_SESSION['username'];
    // $username = "admin";
    $pesan = post("pesanlsg");
    $unsub = post("unsubscribe");
    $jadwal = date("Y-m-d H:i:s");
    if (!empty($_FILES['medialsg']) && $_FILES['medialsg']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['medialsg']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['medialsg']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));

        $allow = ['png', 'jpeg', 'pdf', 'jpg'];
        if (in_array($ext, $allow)) {
            if ($ext == "png") {
                $filename = round(microtime(true)) . mt_rand() . '.png';
            }

            if ($ext == "pdf") {
                $filename = round(microtime(true)) . mt_rand() . '.pdf';
            }

            if ($ext == "jpg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpg';
            }

            if ($ext == "jpeg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpeg';
            }
        } else {
            toastr_set("error", "Format png, jpg, pdf only");
            redirect("kirim.php");
            exit;
        }

        move_uploaded_file($_FILES['medialsg']['tmp_name'], 'uploads/' . $filename);
        // Insert it into our tracking along with the original name
        $media1 = $base_url . "uploads/" . $filename;
        // $carihttp = str_replace("http", "https", $media1);
        // $media = $carihttp;
        $media = $media1;
    } else {
        $media = null;
    }
    foreach ($_POST['targetlsg'] as $nomor => $key) {
        if ($media == null) {
            $nomor = $key;
            sendMSG($nomor, $pesan, $username);
            // $q = mysqli_query($koneksi, "INSERT INTO blast(`nomor`, `pesan`, `jadwal`, `make_by`)
            // VALUES('$nomor', '$pesan', '$jadwal', '$username')");
        } else {
            $nomor = $key;
            // sendMSG($nomor, $media, $username);
            sendMedia($nomor, $pesan, $media, $username);
            // $q = mysqli_query($koneksi, "INSERT INTO blast(`nomor`, `pesan`, `media`, `jadwal`, `make_by`)
            // VALUES('$nomor', '$pesan', '$media', '$jadwal', '$username')");
        }
    }

    // if (isset($_POST['target'])) {
    //     $n = $_POST['target'];
    // } else {
    //     $n = getAllNumber();
    // }

    // $id_blast = getLastID("blast");
    // if (post("tiap_bulan") == "on") {
    //     $tiap_bulan = "1";
    //     $last_month = date("m", strtotime("-1 month"));
    // } else {
    //     $tiap_bulan = "0";
    //     $last_month = date("m", strtotime("-1 month"));
    // }

    // foreach ($n as $nn) {
    //     $pesan = str_replace(['[NAMA]', '[nama]', '[Nama]'], getNama($nn), $pesan);
    //     $pesan = str_replace(['[NOMOR]', '[nomor]', '[Nomor]'], $nn, $pesan);
    //     if ($unsub) {
    //         $pesan = $pesan . "\r\nUntuk berhenti dari info ini balas *UNSUB* atau klik link berikut " . $base_url . "unsub.php?nomor=$nn";
    //     }
    //     // echo $pesan;
    //     $interval = rand(2, 7);
    //     $date = date_create($jadwal);
    //     $jadwal = date_format(date_add($date, date_interval_create_from_date_string("$interval seconds")), 'Y-m-d H:i:s');
    //     // echo $jadwal."<br>";
    //     if ($media == null) {
    //         $q = mysqli_query($koneksi, "INSERT INTO pesan(`id_blast`, `nomor`, `pesan`, `jadwal`, `tiap_bulan`, `last_month`, `make_by`)
    //         VALUES('$id_blast', '$nn', '" . SpinText($pesan) . "', '$jadwal', '$tiap_bulan', '$last_month', '$username')");
    //     } else {
    //         $q = mysqli_query($koneksi, "INSERT INTO pesan(`id_blast`, `nomor`, `pesan`, `media`, `jadwal`,`tiap_bulan`, `last_month`, `make_by`)
    //         VALUES('$id_blast', '$nn', '" . SpinText($pesan) . "', '$media', '$jadwal', '$tiap_bulan', '$last_month', '$username')");
    //     }
    //     sleep(0.2);
    // }
    // exit;
    toastr_set("success", "Sukses kirim pesan terkirim");
}

if (get("act") == "ku") {
    $id_blast = get("id");
    $q = mysqli_query($koneksi, "UPDATE `pesan` SET `status`='MENUNGGU JADWAL' WHERE `id_blast`='$id_blast' AND `status`='GAGAL'");
    toastr_set("success", "Sukses mengirim ulang blast");
    redirect("kirim.php");
}

if (get("act") == "hd") {
    $q = mysqli_query($koneksi, "DELETE FROM pesan WHERE `status`='TERKIRIM' AND `tiap_bulan`='0'");
    toastr_set("success", "Sukses menghapus pesan");
    redirect("kirim.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Messaging Console - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" />
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include_once "sidebar.php"; ?>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                        </li>


                        <!-- Nav Item - Messages -->

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['username'] ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#exampleModal">
                        Kirim Pesan By Schedule
                    </button>
                    <br>
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#kirimpesan">
                        Kirim Pesan Langsung
                    </button>
                    <br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary" style="display:contents">Data Pesan</h6>
                            <a class="btn btn-danger float-right" href="kirim.php?act=hd"><i class="fas fa-trash"></i> Hapus data (terkirim)</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableku" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nomor</th>
                                            <th>Pesan</th>
                                            <th>Media</th>
                                            <th>Jadwal</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($_SESSION['level'] == "1") {
                                            $q = mysqli_query($koneksi, "SELECT * FROM pesan ORDER BY id DESC");
                                        } else {
                                            $username = $_SESSION['username'];
                                            $q = mysqli_query($koneksi, "SELECT * FROM pesan WHERE make_by='$username' ORDER BY id DESC");
                                        }
                                        while ($row = mysqli_fetch_assoc($q)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['id'] . '</td>';
                                            echo '<td>' . $row['nomor'] . '</td>';
                                            echo '<td>' . $row['pesan'] . '</td>';
                                            echo '<td>' . $row['media'] . '</td>';
                                            echo '<td>' . $row['jadwal'] . '</td>';
                                            if ($row['status'] == "TERKIRIM") {
                                                echo '<td><span class="badge badge-success status-container-' . $row['id'] . '">Terkirim</span></td>';
                                            } else if ($row['status'] == "GAGAL") {
                                                echo '<td><span class="badge badge-danger status-container-' . $row['id'] . '">Gagal Terkirim</span></td>';
                                            } else if ($row['tiap_bulan'] == "1") {
                                                echo '<td><span class="badge badge-primary status-container-' . $row['id'] . '">Pengiriman Rutin Setiap Bulan</span></td>';
                                            } else {
                                                echo '<td><span class="badge badge-warning status-container-' . $row['id'] . '">Menunggu Jadwal / Pending</span></td>';
                                            }

                                            if ($row['status'] == "GAGAL") {
                                                echo '<td class="button-container-' . $row['id'] . '"><a style="margin:5px" class="btn btn-success" href="kirim.php?act=ku&id=' . $row['id_blast'] . '">Kirim Ulang</a><a style="margin:5px" class="btn btn-danger" href="hapus_pesan.php?id=' . $row['id'] . '"><i class="fas fa-trash"></i></a></td>';
                                            } else {
                                                echo '<td class="button-container-' . $row['id'] . '"><a class="btn btn-danger" href="hapus_pesan.php?id=' . $row['id'] . '">Hapus</a></td>';
                                            }
                                            echo '</tr>';
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Alucio Net 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Kirim Pesan By Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <label> Pesan * </label>
                        <textarea name="pesan" required class="form-control"></textarea>
                        <code>Sapaan gunakan Array Contoh {Hai|Halo|Ds} <br> Menambahkan Nama/Nomor dengan [Nama][Nomor]</code>
                        <br>
                        <label> Media </label>
                        <input type="file" name="media" class="form-control">
                        <br>
                        <label> Tanggal Pengiriman * </label>
                        <input type="date" name="tgl" required class="form-control">
                        <br>
                        <label> Waktu Pengiriman * </label>
                        <input type="time" name="jam" required class="form-control">
                        <br>
                        <label>Target</label>
                        <br>
                        <select class="form-control js-example-basic-multiple" name="target[]" multiple="multiple" style="width: 100%">
                            <?php
                            if ($_SESSION['level'] == "1") {
                                $q = mysqli_query($koneksi, "SELECT * FROM nomor");
                            } else {
                                $u = $_SESSION['username'];
                                $q = mysqli_query($koneksi, "SELECT * FROM nomor WHERE make_by='$u'");
                            }
                            $q = mysqli_query($koneksi, "SELECT * FROM profiles");
                            while ($row = mysqli_fetch_assoc($q)) {
                                $shownomor = str_replace("@c.us", "", $row['remoteJid']);
                                echo '<option value="' . $shownomor . '">' . $row['name'] . ' (' . $shownomor . ')</option>';
                            }
                            ?>
                        </select>
                        <br>
                        <p>*Kosongkan bila ingin mengirim ke semua kontak</p>
                        <br>
                        <br>
                        <div class="form-check">
                            <input type="checkbox" name="unsubscribe" class="form-check-input" id="unsubscribeCheck1">
                            <label class="form-check-label" for="unsubscribeCheck1">Notif Unsubscribe</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="tiap_bulan" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">Kirim tiap bulan</label>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="kirimpesan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Kirim Pesan Langsung</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <label> Pesan * </label>
                        <textarea name="pesanlsg" required class="form-control"></textarea>
                        <!-- <code>Sapaan gunakan Array Contoh {Hai|Halo|Ds} <br> Menambahkan Nama/Nomor dengan [Nama][Nomor]</code> -->
                        <br>
                        <label> Media </label>
                        <input type="file" name="medialsg" class="form-control">
                        <br>
                        <label>Target</label>
                        <br>
                        <select class="form-control js-example-basic-multiple" name="targetlsg[]" multiple="multiple" style="width: 100%">
                            <?php
                            if ($_SESSION['level'] == "1") {
                                $q = mysqli_query($koneksi, "SELECT * FROM nomor");
                            } else {
                                $u = $_SESSION['username'];
                                $q = mysqli_query($koneksi, "SELECT * FROM nomor WHERE make_by='$u'");
                            }
                            $q = mysqli_query($koneksi, "SELECT * FROM profiles");
                            while ($row = mysqli_fetch_assoc($q)) {
                                $shownomor = str_replace("@c.us", "", $row['remoteJid']);
                                echo '<option value="' . $shownomor . '">' . $row['name'] . ' (' . $shownomor . ')</option>';
                            }
                            ?>
                        </select>
                        <br>
                        <!-- <p>*Kosongkan bila ingin mengirim ke semua kontak</p> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tableku').DataTable();
        });
    </script>
    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
    <script>
        <?php

        toastr_show();

        ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                dropdownAutoWidth: true
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
    <script>
        setInterval(sync, 4000);

        function sync() {
            let sync = localStorage.getItem('sync');
            if (sync == null) {
                sync = moment().format("YYYY-MM-DD HH:mm:ss");
                localStorage.setItem('sync', sync);
            }

            $.get("longpooling.php?lastsync=" + sync, function(data) {
                r = JSON.parse(data);

                jQuery.each(r, function(i, val) {
                    let id = val.id;
                    let id_blast = val.id_blast;
                    if (val.status == "GAGAL") {
                        $(".status-container-" + id).empty();
                        $(".status-container-" + id).html('Gagal Terkirim');
                        $(".status-container-" + id).addClass('badge-danger').removeClass('badge-warning');

                        $(".button-container-" + id).html('<a style="margin:5px" class="btn btn-success" href="kirim.php?act=ku&id=' + id_blast + '">Kirim Ulang</a><a style="margin:5px" class="btn btn-danger" href="hapus_pesan.php?id=' + id + '">Hapus</a>');
                    }

                    if (val.status == "TERKIRIM") {
                        $(".status-container-" + id).empty();
                        $(".status-container-" + id).html('Terkirim');
                        $(".status-container-" + id).addClass('badge-success').removeClass('badge-warning');

                        $(".button-container-" + id).html('<a style="margin:5px" class="btn btn-danger" href="hapus_pesan.php?id=' + id + '">Hapus</a>');
                    }
                    console.log(id);
                });

                localStorage.setItem('sync', moment().format("YYYY-MM-DD HH:mm:ss"));

            });
        }
    </script>
</body>

</html>