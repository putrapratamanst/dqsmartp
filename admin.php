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
                            <h4 class="page-title">Student Accounts</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->


                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>School</th>
                                            <th>State</th>
                                            <th>Activate</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            include 'program/koneksi.php';

                                        $sql = "SELECT * FROM `account`";
                                        $result = $conn->query($sql);
                                        $username = '';
                                        if ($result->num_rows > 0) {
                                            // output data of each row
                                            $no = 1;
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<th scope=\"row\">$no</th>";
                                                echo "<td>" . $row['USERNAME'] . "</td>";
                                                echo "<td>" . $row['EMAIL'] . "</td>";
                                                echo "<td>" . $row['SCHOOL'] . "</td>";

                                                if($row['RANK'] == "super")
                                                {
                                                    echo "<td><button type=\"button\" class=\"btn btn-info rounded-pill waves-effect waves-light\"> <span class=\"btn-label\"><i class=\"mdi mdi-alert-circle-outline\"></i></span>ADMIN</button></td>";
                                                    echo "<td></td>";
                                                }
                                                else
                                                {
                                                    if ($row['STATE'] == "upload")
                                                    {
                                                        echo "<td><a href='image.php?ID=" . $row['ID'] . "'><button type=\"button\" class=\"btn btn-success rounded-pill waves-effect waves-light\"> <span class=\"btn-label\"><i class=\"mdi mdi-check-all\"></i></span>UPLOAD</button></a></td>";
                                                        echo "<td><a href='activate.php?id=" . $row['ID'] . "'><button type=\"button\" class=\"btn btn-success rounded-pill waves-effect waves-light\"> <span class=\"btn-label\"><i class=\"mdi mdi-check\"></i></span>ACTIVATE</button></a></td>";
                                                    }
                                                    else
                                                    {
                                                        echo "<td><button type=\"button\" class=\"btn btn-info rounded-pill waves-effect waves-light\"> <span class=\"btn-label\"><i class=\"mdi mdi-alert-circle-outline\"></i></span>" . $row['STATE'] . "</button></td>";
                                                        echo "<td></td>";
                                                    }
                                                }
                                                $username = $row['EMAIL'];
                                                $no++;
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }

                                        include 'program/close.php';
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
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

<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>

</body>
</html>