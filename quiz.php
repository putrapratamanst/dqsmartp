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
    include 'program/koneksi.php';
    $sql = "SELECT COUNT(*) TOTAL FROM `QUESTION`";
    $tot = 0;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        $no = 1;
        while($row = $result->fetch_assoc()) {
            $tot = $row['TOTAL'];
        }
    }
    $lang = isset($_SESSION['lang'])?$_SESSION['lang']:"";
    $lang = $lang=="" ? "id" : $lang;
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
                            <h4 class="page-title">Question <?php echo $_GET['no'] . ' Of ' . $tot;?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->


                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                $nomor = $_GET['no'];

                                $sql = "SELECT * FROM `QUESTION` WHERE ID = $nomor";
                                $result = $conn->query($sql);
                                $username = '';
                                $type = '';
                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    $no = 1;
                                    while($row = $result->fetch_assoc()) {
                                        $anx = 0;
                                        $answer = $_GET['answer'];
                                        $rayans = explode('L', $_GET['answer']);
                                        $anx = $rayans[$nomor-1];
                                        $type = $row['NILAI'];
                                        if($row['NILAI'] != 1)
                                        {
                                            $anx = explode("X", $anx);
                                        }
                                        echo $lang=="id"?$row['QUESTION']:$row['QUESTION_EN'];
                                        echo "<br>";
                                        echo "<br>";
                                        $sql = "SELECT * FROM `ANSWER` WHERE QUESTION = $nomor";
                                        $result = $conn->query($sql);
                                        $username = '';
                                        $ans = explode(",",$_SESSION['answer']);
                                        if ($result->num_rows > 0) {
                                            // output data of each row
                                            $ns = 0;
                                            $nos = 1;
                                            while($row = $result->fetch_assoc()) {
                                                $jawaban = $anx[$ns];

                                                $answerText = $lang=="id"?$row['ANSWER']:$row['ANSWER_EN'];

                                                if ($nos == $jawaban)
                                                {
                                                    echo "<a href='picker.php?pick=$nos&no=$nomor&answer=$answer&type=$type'><button type=\"button\" style='width: 50%;' class=\"btn btn-warning waves-effect waves-light $anx\">" . $answerText . "</button></a>";
                                                    echo "<br>";
                                                    echo "<br>";
                                                }
                                                else
                                                {
                                                    echo "<a href='picker.php?pick=$nos&no=$nomor&answer=$answer&type=$type'><button type=\"button\" style='width: 50%;' class=\"btn btn-success waves-effect waves-light $anx\">" . $answerText . "</button></a>";
                                                    echo "<br>";
                                                    echo "<br>";
                                                }
                                                $nos++;
                                                $ns++;
                                            }

                                        } else {
                                            echo "0 results";
                                        }
                                        $no++;
                                    }

                                } else {
                                    echo "0 results";
                                }

                                include 'program/close.php';
                                ?>
                            </div>

                        </div>
                    <br>
                        <br>
                        <?php
                        if($nomor < 47)
                        {
                            if ($nomor > 1)
                            {
                                $noback = $nomor - 1;
                                if($type == 1)
                                {
                                    /*echo "<a href='quiz.php?no=$noback&answer=$answer'><button type=\"button\" class=\"btn btn-danger waves-effect waves-light\">BACK</button></a>";*/
                                    
                                }
                                else
                                {
                                    $noback = $nomor - 1;
                                    $next = $nomor + 1;
                                    echo "<table width='100%'><tr><td><a href='quiz.php?no=$noback&answer=$answer'><button type=\"button\" class=\"btn btn-danger waves-effect waves-light\">BACK</button></a></td><td><a href='quiz.php?no=$next&answer=$answer'><button type=\"button\" class=\"btn btn-success waves-effect waves-light\">NEXT</button></a></td><td></td><td></td><td></td></tr></table>";
                                }
                            }
                        }
                        else
                        {
                            $noback = $nomor - 1;
                            /*echo "<table width='100%'><tr><td><a href='quiz.php?no=$noback&answer=$answer'><button type=\"button\" class=\"btn btn-danger waves-effect waves-light\">BACK</button></a></td><td><a href='uploadresult.php?no=$nomor&answer=$answer'><button type=\"button\" class=\"btn btn-success waves-effect waves-light\">FINISH</button></a></td><td></td><td></td><td></td></tr></table>";*/
                            
                            echo "<table width='100%'>";
                            echo "    <tr>";
                            /*echo "        <td>";
                            echo "            <a href='quiz.php?no=$noback&answer=$answer'>";
                            echo "                <button type=\"button\" class=\"btn btn-danger waves-effect waves-light\">BACK</button>";
                            echo "            </a>";
                            echo "        </td>";*/
                                    
                            echo "        <td>";
                            echo "            <a href='uploadresult.php?no=$nomor&answer=$answer'>";
                            echo "                <button type=\"button\" class=\"btn btn-success waves-effect waves-light\">FINISH</button>";
                            echo "            </a>";
                            echo "        </td>";
                            echo "        <td></td>";
                            echo "        <td></td>";
                            echo "        <td></td>";
                            echo "    </tr>";
                            echo "</table>";
                        }
                        ?>
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
        <?php echo "console.log('" . $_SESSION['answer'] . "');"; ?>
    })
</script>
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>

</body>
</html>