<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign'])) {
    include 'program/koneksi.php';
    foreach($_POST['assign'] as $voucher => $user) {
        if (!empty($user)) {
            $stmt = $conn->prepare("UPDATE VOUCHERS SET ASSIGN_ON = ? WHERE VOUCHER = ?");
            $stmt->bind_param("ss", $user, $voucher);
            $stmt->execute();
            $stmt->close();
        }
    }
    include 'program/close.php';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
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

                            <h4 class="page-title">Voucher List</h4>

                        </div>

                    </div>

                </div>

                <!-- end page title -->





                <div class="row">

                    <div class="col-lg-12">

                        <div class="card">

                            <div class="card-body">

                                <div class="dropdown float-end">

                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">

                                        <i class="mdi mdi-dots-vertical"></i>

                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end">

                                        <!-- item-->

                                        <a href="addvoucher.php" class="dropdown-item">Add Voucher</a>

                                    </div>

                                </div>

                                <form method='post'>

                                <div class="table-responsive">

                                    <table class="table mb-0">

                                        <thead>

                                        <tr>

                                            <th>#</th>

                                            <th>Voucher Code</th>
                                            <th>Assign On</th>
                                            <th>State</th>

                                            <th>Created At</th>

                                            <th>Keterangan</th>

                                        </tr>

                                        </thead>

                                        <tbody>

                                        <?php

                                        include 'program/koneksi.php';

                                        $sql = "SELECT * FROM `VOUCHERS`  ORDER BY CREATED_AT DESC ";

                                        $result = $conn->query($sql);

                                        $username = '';

                                        if ($result->num_rows > 0) {

                                            // output data of each row

                                            $no = 1;

                                            while($row = $result->fetch_assoc()) {
                                                
                                                echo "<th scope=\"row\">$no</th>";

                                                echo "<td>" . $row['VOUCHER'] . "</td>";
                                                if (is_null($row['ASSIGN_ON']) || trim($row['ASSIGN_ON']) == '') {
                                                    echo "<td><input type='email' name='assign[" . htmlspecialchars($row['VOUCHER']) . "]' class='form-control' placeholder='Masukkan Email Calon Pengguna'></td>";
                                                } else {
                                                    echo "<td>" . htmlspecialchars($row['ASSIGN_ON']) . "</td>";
                                                }
                                                if ($row['STATE'] == 'Y') {
                                                    echo "<td class='bg-danger text-white'>sudah dipakai</td>";
                                                } else {
                                                    echo "<td class='bg-success text-white'>belum dipakai</td>";
                                                }
                                                echo "<td>" . $row['CREATED_AT'] . "</td>";

                                                echo "<td>" . $row['KETERANGAN'] . "</td>";

                                                // $username = $row['EMAIL'];

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

                                <button type='submit' class='btn btn-primary mt-3'>Assign Selected Vouchers</button>

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



<!-- Vendor js -->

<script src="assets/js/vendor.min.js"></script>



<!-- App js -->

<script src="assets/js/app.min.js"></script>



</body>

</html>