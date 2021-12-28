<?php
include_once("configuration.inc");
$activity[0] = "messaging";
$activity[1] = "contacts_main";
include_once("header.php");
include_once("includes/koneksi.php");
include_once("includes/function.php");

if (!isset($user_session)) {
    $login = cekSession();
    if ($login == 0) {
        redirect("login.php");
    }
}

$active_menu = "autoreply";

if (post("keyword")) {
    // if (isset($_POST['response']) && empty($_POST['targetlsg'])) {
    //     toastr_set("error", "Pastikan Target Forwading Terisi");
    // } else {
    $responseforwading = post("responseforwading");
    $keyword = post("keyword");
    $response = post("response");
    $case_sensitive = post("case_sensitive");
    if (isset($_POST['targetlsg'])) {
        $target1 = $_POST['targetlsg'];
        $target = implode(",", $target1);
        $targetandresponse = $target . "¶" . $response;
    } else {
        $target = NULL;
        $targetandresponse = $target;
    }

    if ($case_sensitive == "") {
        $case_sensitive = "0";
    } else {
        $case_sensitive = "1";
    }
    //Get Response Forwading 
    $q = mysqli_query($koneksi, "INSERT INTO autoreply(`keyword`, `response`, `case_sensitive`,`forward_destinations`)
            VALUES('$keyword', '$responseforwading', '$case_sensitive','$targetandresponse')");
    toastr_set("success", "Sukses menambahkan Auto Response");
    // }
}
if (post("id-edit")) {
    // if (isset($_POST['response-edit']) && empty($_POST['targetlsg'])) {
    //     toastr_set("error", "Pastikan Target Forwading Terisi");
    // } else {
    $idedit = post("id-edit");
    $responseforwading = post("responseforwading-edit");
    $keyword = post("keyword-edit");
    $response = post("response-edit");
    $case_sensitive = post("case_sensitive-edit");
    if (isset($_POST['targetlsg-edit'])) {
        $target1 = $_POST['targetlsg-edit'];
        $target = implode(",", $target1);
        $targetandresponse = $target . "¶" . $response;
    } else {
        $target = NULL;
        $targetandresponse = $target;
    }

    if ($case_sensitive == "") {
        $case_sensitive = "0";
    } else {
        $case_sensitive = "1";
    }
    if ($response !== NULL) {
    } else {
    }
    //Get Response Forwading 
    $q = mysqli_query($koneksi, "UPDATE autoreply set keyword='$keyword',response='$responseforwading',case_sensitive='$case_sensitive',forward_destinations='$targetandresponse' where id ='$idedit'");
    toastr_set("success", "Sukses Edit Auto Response");
    // }
}

if (get("act") == "hapus") {
    $id = get("id");

    $q = mysqli_query($koneksi, "DELETE FROM autoreply WHERE id='$id'");
    toastr_set("success", "Sukses menghapus Auto Response");
    redirect("auto_reply.php");
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

    <title>WA Console - Auto Response</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" />
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
                        Tambah Auto Response
                    </button>
                    <br>
                    <div class="card shadow mb-4">

                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Auto Response</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Keyword</th>
                                            <th>Response</th>
                                            <th>Case Sensitive</th>
                                            <th width="10%">Target Destination</th>
                                            <th width="20%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $q = mysqli_query($koneksi, "SELECT * FROM autoreply");
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($q)) {
                                            $forward_destinations = $row['forward_destinations'];
                                            echo '<tr>';
                                            echo '<td>' . $no++ . '</td>';
                                            echo '<td>' . $row['keyword'] . '</td>';
                                            echo '<td>' . $row['response'] . '</td>';
                                            if ($row['case_sensitive'] == "0") {
                                                echo '<td><span class="badge badge-primary">Non Sensitive</span></td>';
                                            } else {
                                                echo '<td><span class="badge badge-danger">Sensitive</span></td>';
                                            }
                                            echo "<td>$forward_destinations</td>";
                                            echo '<td><a class="btn btn-primary" href="#" onclick="editreply(' . $row['id'] . ')"><i class="fas fa-edit"></i></a>&nbsp;<a class="btn btn-danger" href="auto_reply.php?act=hapus&id=' . $row['id'] . '"><i class="fas fa-trash"></i></a></td>';
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
                        <span>Copyright &copy; DVLPR 2021</span>
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
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Auto Response</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <label> Keyword </label>
                        <input type="text" name="keyword" required class="form-control">
                        <br>
                        <label> Response to Sender </label>
                        <textarea name="responseforwading" class="form-control" required></textarea>
                        <br>
                        <div class="form-check">
                            <input type="checkbox" name="case_sensitive" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">Case Sensitive ?</label>
                        </div>
                        <label> Target Forwading</label>
                        <select class="form-control js-example-basic-multiple" name="targetlsg[]" id="targetlsg" multiple="multiple" style="width: 100%">
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
                        <div id="response-add-div">
                            <hr>
                            <label>Forwarding Notification</label>
                            <textarea name="response" class="form-control"></textarea>
                            <br>
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
    <!-- Modal Edit-->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Auto Response</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <label> Keyword </label>
                        <input type="hidden" name="id-edit" id="id-edit" required class="form-control">
                        <input type="text" name="keyword-edit" id="keyword-edit" required class="form-control">
                        <br>
                        <label> Response to Sender</label>
                        <textarea type="text" name="responseforwading-edit" id="responseforwading-edit" required class="form-control"></textarea>
                        <br>
                        <div class="form-check">
                            <input type="checkbox" name="case_sensitive-edit" class="form-check-input" id="case_sensitive-edit">
                            <label class="form-check-label" for="case_sensitive-edit">Case Sensitive ?</label>
                        </div>
                        <label> Target Forwading</label>
                        <select class="form-control js-example-basic-multiple" name="targetlsg-edit[]" id="targetlsg-edit" multiple="multiple" style="width: 100%">
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
                        <hr>
                        <div id="response-edit-div">
                            <label> Forwarding Notification</label>
                            <textarea name="response-edit" id="response-edit" class="form-control"></textarea>
                            <br>
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


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                dropdownAutoWidth: true
            });
        });


        function editreply(id) {
            var url = "./getdatareply.php";
            $.ajax({
                url: url,
                data: {
                    id: id
                },
                type: "POST"
            }).done(function(data) {
                var json = data,
                    obj = JSON.parse(json)
                console.log(obj);
                $("#id-edit").val(obj.id)
                $("#keyword-edit").val(obj.keyword)
                $("#responseforwading-edit").val(obj.response)
                if (obj.case_sensitive === "0") {
                    $("#case_sensitive-edit").prop("checked", false);
                } else {
                    $("#case_sensitive-edit").prop("checked", true);
                }
                $("#targetlsg-edit").val(obj.forward_destinations).trigger('change');
                // $("#targetlsg-edit").val(obj.)
                $("#response-edit").val(obj.responsepesan)
                $("#modalEdit").modal('show');

            });
        }
    </script>
    <script>
        <?php

        toastr_show();

        ?>
    </script>
</body>

</html>