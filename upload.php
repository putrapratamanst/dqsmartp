<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>DQ-Smartplus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link href="assets/libs/dropzone/min/dropzone.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/dropify/css/dropify.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="assets/css/config/default/bootstrap.min.css" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="assets/css/config/default/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

    <link href="assets/css/config/default/bootstrap-dark.min.css" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled="disabled" />
    <link href="assets/css/config/default/app-dark.min.css" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled="disabled" />

    <!-- icons -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>

</head>

<!-- body start -->
<body class="loading" data-layout-mode="horizontal" data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

<!-- Begin page -->
<div id="wrapper">

    <?php
    include 'program/studheader.php';
    ?>
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="content-page">
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box page-title-box-alt">
                            <h4 class="page-title">Upload</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" id="content">
                                <form method="post" action="uploadpict.php" id="frm" enctype="multipart/form-data">
                                <h4 class='header-title'>Upload Bukti Pembayaran</h4>
                                <p class='sub-header'> Upload bukti pembayaran kamu disini </p>
                                <input type='file' id="uploaded" name='uploaded' data-plugins='dropify' data-height='300'/>
                                <br>
                                <button type="submit" class="btn btn-primary rounded-pill waves-effect waves-light">Upload</button>
                                </form>
                                <form method="post" action="deletefile.php" id="frm2">

                                </form>
                            </div>

                        </div> <!-- end card-->
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <!-- file preview template -->
                <div class="d-none" id="uploadPreviewTemplate">
                    <div class="card mt-1 mb-0 shadow-none border">
                        <div class="p-2">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img name="sourcepicture" data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                                </div>
                                <div class="col ps-0">
                                    <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                                    <p class="mb-0" data-dz-size></p>
                                </div>
                                <div class="col-auto">
                                    <!-- Button -->
                                    <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                                        <i class="dripicons-cross"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- end row -->

                <!--- end row -->
                <form action="uploadpict.php" method="post" hidden>
                    <input type="text" name="fileimage" id="fileimage">
                    <button type="submit" id="subm"></button>
                </form>
            </div> <!-- container-fluid -->
        </div> <!-- content -->

        <!-- Footer Start -->
        <?php
        include 'program/footer.php';
        ?>
        <!-- end Footer -->

    </div>

    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->


</div>
<!-- END wrapper -->
<script>
    $(document).ready(function () {
        reset();
    })

    function reset() {
        <?php

        echo "console.log('Running');";

        $id = $_SESSION['ID'];
        include 'program/koneksi.php';
        echo "console.log('Running 2 - $id');";
        $sql = "SELECT COUNT(*) AS TOTAL FROM `IMAGE` WHERE USERID = " . $id;
        echo "console.log('Running 3 - $sql');";
        $result = $conn->query($sql);
        echo "console.log('Running 4');";
        $hasil = 0;
        echo "console.log('Running 5');";
        if ($result->num_rows > 0) {
            echo "console.log('Running 6');";
            while($row = $result->fetch_assoc()) {
                $hasil = $row['TOTAL'];
                echo "console.log('Total : $hasil');";
            }
        } else {
        }

        if ($hasil>0)
        {
            $sql = "SELECT * FROM `IMAGE` WHERE USERID = " . $_SESSION['ID'] . " ORDER BY UPLOAD_AT";
            $result = $conn->query($sql);
            $gambar = '';
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $gambar = $row['IMAGE'];
                }
            } else {
                echo "0 results";
            }
            echo "$('#frm').html(\"\");";
            echo "$('#frm2').html(\"<img width='300' height='300' src='upload/$gambar'></br></br><button type='submit' class='btn btn-warning rounded-pill waves-effect waves-light'>Delete</button> \");";
        }
        ?>
    }

    function upload() {
        var gambar = $('.dropify-render img').attr('src');
        $('#fileimage').val(gambar);
        $('#subm').click();
        console.log(gambar);
    }
</script>
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>

<!-- Plugins js -->
<script src="assets/libs/dropzone/min/dropzone.min.js"></script>
<script src="assets/libs/dropify/js/dropify.min.js"></script>

<!-- Init js-->
<script src="assets/js/pages/form-fileuploads.init.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>

</body>
</html>