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
    include 'program/header.php';
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
                            <h4 class="page-title">Add Voucher</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->


                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <br>
                                <form method="post" action="prog_question.php">
                                    <div>
                                        <label for="example-number" class="form-label">Sequence</label>
                                        <input class="form-control" id="id" type="number" name="id" <?php echo "value=\"" . $_GET['ID'] . "\""?>>
                                    </div>
                                    <?php
                                    include 'program/koneksi.php';

                                    $sql = "SELECT * FROM `QUESTION` WHERE ID = " . $_GET['ID'];
                                    $result = $conn->query($sql);
                                    $username = '';
                                    if ($result->num_rows > 0) {
                                        // output data of each row
                                        $no = 1;
                                        while($row = $result->fetch_assoc()) {
                                            $username = $row['QUESTION'];
                                        }
                                    } else {
                                        echo "0 results";
                                    }

                                    include 'program/close.php';
                                    ?>
                                    <div>
                                        <label for="example-number" class="form-label">Question</label>
                                        <input class="form-control" id="question" type="text" name="question" <?php echo "value=\"" . $username . "\""?>>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                                </form>
                            </div>

                        </div>

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
    $(document).ready(function () {
        reset();
    })

    function reset() {
        $('#totalvoucher').val('0')

    }
</script>
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>

</body>
</html>