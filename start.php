<?php
//session_start();
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
    $lang = isset($_SESSION['lang'])?$_SESSION['lang']:"";
    if($lang == "") {
        $lang = "id";
    }
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
                            <h4 class="page-title"><?php echo $lang=="id"?"Selamat datang di DQ-Smartplus Digital Quotient Test":"Welcome to DQ-Smartplus Digital  Quotient Test"; ?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->


                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                    echo $lang=="id" ? "Hi! Terima Kasih, anda  akan mengikuti Test  Kecerdasan Digital (Digital  Quotient). Test ini  terdiri dari 47 Pertanyaan, yang harus di jawab langsung tanpa ada tombol refresh atau pengulangan. Tujuan dari test ini adalah untuk mengukur tingkat kecerdasan digital dalam menghadapi tantangan dan beradaptasi dengan tuntutan kehidupan digital. Jika anda sudah siap, silahkan tekan tombol mulai." : "Hi! You will take the Digital Intelligence Quotient Test. This test consists of 47 questions, which must be answered directly without any refresh buttons. The purpose of this test is to measure the level of digital intelligence in facing challenges and adapting to the digital life. If you are ready, please press START button. Thank you."
                                ?>
                                <br>
                                <br>
                                <a href='quizstarter.php'><button type="button" class="btn btn-success waves-effect waves-light"><?php echo $lang=="id" ? "MULAI" : "START"; ?></button></a>
                            </div>

                        </div>
                    <br>
                        <br>
                        
                    </div>
                </div>
                <!--- end row -->


                <!--- end row -->

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

</script>
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>

</body>
</html>