<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8" />

    <title>Report - DQ-Smartplus</title>

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



    <style>
        .table th {

            vertical-align: middle;

            text-align: center;

            font-size: 12px;

            padding: 10px 5px;

        }



        .svg-icon {

            display: block;

            margin: 0 auto 5px auto;

        }



        .export-btn {

            font-size: 11px;

            padding: 4px 8px;

        }
    </style>



</head>

<body class="loading" data-layout-mode="horizontal" data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

    <div id="wrapper">

        <?php

        include 'program/header.php';

        include 'program/koneksi.php';



        $sqlSchool = "SELECT DISTINCT SCHOOL FROM `account` WHERE STATE ='FINISH' ORDER BY SCHOOL";

        $resultSchool = $conn->query($sqlSchool);



        $param_school = isset($_GET['school']) ? $_GET['school'] : "";

        $from_date = date("Y-m-d", strtotime("-6 months"));

        $to_date = date("Y-m-d");



        if (isset($_GET['fromDate'])) {

            if ($_GET['fromDate'] != "") {

                $from_date = $_GET['fromDate'];
            }
        }



        if (isset($_GET['toDate'])) {

            if ($_GET['toDate'] != "") {

                $to_date = $_GET['toDate'];
            }
        }



        $sql = "

                SELECT

                    A.SCHOOL,

                    IFNULL(A.GRADE, 'Tidak Ada Kelas') AS GRADE,

                    (SELECT COUNT(ID) FROM account AS a1 WHERE a1.SCHOOL = A.SCHOOL AND a1.STATE = 'FINISH' AND a1.GRADE = A.GRADE) AS COUNT_OF_STUDENT,

                    SUM(CASE WHEN Q.TIPE = 'Critical Thinking' THEN R.VALUE ELSE 0 END) AS 'critical_thinking',

                    SUM(CASE WHEN Q.TIPE = 'Cyber Security Management' THEN R.VALUE ELSE 0 END) AS 'cyber_security_management',

                    SUM(CASE WHEN Q.TIPE = 'Cyberbullying' THEN R.VALUE ELSE 0 END) AS 'cyberbullying',

                    SUM(CASE WHEN Q.TIPE = 'Digital Citizen Identity' THEN R.VALUE ELSE 0 END) AS 'digital_citizen_identity',

                    SUM(CASE WHEN Q.TIPE = 'Digital Empathy' THEN R.VALUE ELSE 0 END) AS 'digital_empathy',

                    SUM(CASE WHEN Q.TIPE = 'Digital Footprint' THEN R.VALUE ELSE 0 END) AS 'digital_footprint',

                    SUM(CASE WHEN Q.TIPE = 'Privacy Management' THEN R.VALUE ELSE 0 END) AS 'privacy_management',

                    SUM(CASE WHEN Q.TIPE = 'Screen Time' THEN R.VALUE ELSE 0 END) AS 'screen_time'

                FROM

                    RESULT AS R

                    LEFT JOIN QUESTION AS Q ON Q.ID = R.QUESTION

                    LEFT JOIN CATEGORY AS C ON C.KATEGORI = Q.TIPE

                    LEFT JOIN account AS A ON A.ID = R.USERID

                WHERE

                    A.STATE = 'FINISH' ";

        //-- AND A.SCHOOL = 'SMAK Sang Timur'



        if ($param_school != "") {

            $sql .= " AND A.SCHOOL = '" . $param_school . "'";
        }



        if ($from_date != "") {

            $sql .= " AND R.ACTIVITY_ON >= '" . $from_date . " 00:00:00'";
        }



        if ($to_date != "") {

            $sql .= " AND R.ACTIVITY_ON <= '" . $to_date . " 23:59:00'";
        }



        $sql .= " GROUP BY

                    A.SCHOOL,

                    A.GRADE

                ORDER BY

                    A.SCHOOL,

                    A.GRADE

            ";

        $result = $conn->query($sql);



        function getBgColor($nilai)
        {

            $warna = "";

            if ($nilai > 115) {

                $warna = "#260e83";
            } elseif ($nilai >= 100) {

                $warna = "#f58a0a";
            } elseif ($nilai >= 85) {

                $warna = "#ed2207";
            } else {

                $warna = "#c40010";
            }



            return $warna;
        }

        ?>



        <div class="content-page">

            <div class="content">



                <!-- Start Content-->

                <div class="container-fluid">

                    <!-- start page title -->

                    <div class="row">

                        <div class="col-12">

                            <div class="page-title-box page-title-box-alt">

                                <h4 class="page-title">Report</h4>

                            </div>

                        </div>

                    </div>

                    <!-- end page title -->





                    <div class="row">

                        <div class="col-lg-12">

                            <div class="card">

                                <div class="card-body">

                                    <form>

                                        <div class="row mb-2">

                                            <label for="school" class="col-md-2 col-form-label col-form-label-sm">School</label>

                                            <div class="col-md-4">

                                                <select class="form-select" id="school" name="school">

                                                    <?php

                                                    echo '<option value="">-- choose school --</option>';

                                                    while ($row = $resultSchool->fetch_assoc()) {

                                                        if ($param_school == $row['SCHOOL']) {

                                                            echo '<option selected value="' . $row['SCHOOL'] . '">' . $row['SCHOOL'] . '</option>';
                                                        } else {

                                                            echo '<option value="' . $row['SCHOOL'] . '">' . $row['SCHOOL'] . '</option>';
                                                        }
                                                    }

                                                    ?>

                                                </select>

                                            </div>

                                        </div>

                                        <div class="row mb-2">

                                            <label for="fromDate" class="col-md-2 col-form-label col-form-label-sm">School</label>

                                            <div class="col-md-2">

                                                <input type="date" class="form-control" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />

                                            </div>

                                            <div class="col-md-2">

                                                <input type="date" class="form-control" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />

                                            </div>

                                        </div>

                                        <div class="row mb-2">

                                           
                                            <div class="col-md-2 d-flex gap-2 offset-md-2">
                                                <button class="btn btn-primary" type="submit">Search</button>
                                                <a href="report.php" class="btn btn-secondary">Clear</a>
                                            </div>

                                            <div class="col-md-2">

                                                <a href="report_pdf.php?<?php echo 'school=' . $param_school . '&fromDate=' . $from_date . '&toDate=' . $to_date; ?>" 
                                                   class="btn btn-danger <?php echo empty($param_school) ? 'disabled' : ''; ?>" 
                                                   target="_blank"
                                                   <?php echo empty($param_school) ? 'onclick="return false;"' : ''; ?>>

                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white" style="margin-right: 5px;">

                                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />

                                                    </svg>

                                                    Export All to PDF

                                                </a>

                                            </div>

                                        </div>



                                    </form>



                                    <table class="table table-bordered mb-0" width="100%">

                                        <thead class="thead-dark">

                                            <tr>

                                                <th class="align-middle">School</th>

                                                <th class="align-middle">Grade</th>

                                                <th class="align-middle">Total Students</th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <circle cx="50" cy="50" r="40" fill="#FFE082" />
                                                        <path d="M30 45 L45 60 L70 35" stroke="#333" stroke-width="3" fill="none" />
                                                        <circle cx="35" cy="35" r="8" fill="#FF5722" />
                                                        <circle cx="65" cy="35" r="8" fill="#2196F3" />
                                                    </svg>
                                                    Critical Thinking
                                                </th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <path d="M50 10 L20 25 L20 55 C20 75 50 90 50 90 C50 90 80 75 80 55 L80 25 Z" fill="#4CAF50" stroke="#333" stroke-width="2" />
                                                        <circle cx="65" cy="45" r="8" fill="#FF5722" />
                                                    </svg>
                                                    Cyber Security Management
                                                </th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <circle cx="50" cy="50" r="40" fill="#F44336" />
                                                        <path d="M30 30 L70 70 M70 30 L30 70" stroke="white" stroke-width="6" />
                                                        <rect x="60" y="20" width="15" height="25" rx="3" fill="#2196F3" />
                                                    </svg>
                                                    Cyberbullying
                                                </th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <rect x="15" y="20" width="70" height="50" rx="5" fill="#E3F2FD" stroke="#1976D2" stroke-width="2" />
                                                        <circle cx="35" cy="40" r="8" fill="#FFB74D" />
                                                        <path d="M50 35 Q65 25 75 40 Q65 55 50 45" fill="#FF8A65" />
                                                    </svg>
                                                    Digital Citizen Identity
                                                </th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <path d="M30 40 Q30 25 50 25 Q70 25 70 40 Q70 55 50 70 Q30 55 30 40 Z" fill="#E91E63" />
                                                        <path d="M45 30 Q45 20 55 20 Q65 20 65 30 Q65 40 55 50 Q45 40 45 30 Z" fill="#2196F3" />
                                                    </svg>
                                                    Digital Empathy
                                                </th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <circle cx="50" cy="50" r="30" fill="#81C784" />
                                                        <path d="M40 35 L60 35 L70 50 L60 65 L40 65 L30 50 Z" fill="none" stroke="#333" stroke-width="2" />
                                                        <path d="M50 20 Q60 30 70 40 Q60 50 50 60 Q40 50 30 40 Q40 30 50 20" fill="#FF5722" />
                                                    </svg>
                                                    Digital Footprint
                                                </th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <rect x="20" y="30" width="60" height="40" rx="5" fill="#E1F5FE" stroke="#0277BD" stroke-width="2" />
                                                        <circle cx="50" cy="50" r="4" fill="#0277BD" />
                                                        <path d="M30 45 L40 45 M60 45 L70 45 M30 55 L45 55 M55 55 L70 55" stroke="#0277BD" stroke-width="2" />
                                                    </svg>
                                                    Privacy Management
                                                </th>

                                                <th class="align-middle" width="8%">
                                                    <svg width="20" height="20" viewBox="0 0 100 100" class="svg-icon">
                                                        <circle cx="50" cy="50" r="35" fill="#E3F2FD" stroke="#1976D2" stroke-width="3" />
                                                        <path d="M50 20 L50 50 L70 60" stroke="#1976D2" stroke-width="3" fill="none" />
                                                        <circle cx="50" cy="50" r="3" fill="#1976D2" />
                                                    </svg>
                                                    Screen Time
                                                </th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php
                                            while ($row = $result->fetch_assoc()) {
                                                $critical_thinking = $row['critical_thinking'] / $row['COUNT_OF_STUDENT'];

                                                $cyber_security_management = $row['cyber_security_management'] / $row['COUNT_OF_STUDENT'];

                                                $cyberbullying = $row['cyberbullying'] / $row['COUNT_OF_STUDENT'];

                                                $digital_citizen_identity = $row['digital_citizen_identity'] / $row['COUNT_OF_STUDENT'];

                                                $digital_empathy = $row['digital_empathy'] / $row['COUNT_OF_STUDENT'];

                                                $digital_footprint = $row['digital_footprint'] / $row['COUNT_OF_STUDENT'];

                                                $privacy_management = $row['privacy_management'] / $row['COUNT_OF_STUDENT'];

                                                $screen_time = $row['screen_time'] / $row['COUNT_OF_STUDENT'];



                                                $critical_thinking_color = "";

                                                if ($critical_thinking > 115) {

                                                    $critical_thinking_color = "#260e83";
                                                } elseif ($critical_thinking >= 100) {

                                                    $critical_thinking_color = "#f58a0a";
                                                } elseif ($critical_thinking >= 85) {

                                                    $critical_thinking_color = "#ed2207";
                                                } else {

                                                    $critical_thinking_color = "#c40010";
                                                }



                                                $critical_thinking = round($critical_thinking, 0) * 1;

                                                $cyber_security_management = round($cyber_security_management, 0) * 1;

                                                $cyberbullying = round($cyberbullying, 0) * 1;

                                                $digital_citizen_identity = round($digital_citizen_identity, 0) * 1;

                                                $digital_empathy = round($digital_empathy, 0) * 1;

                                                $digital_footprint = round($digital_footprint, 0) * 1;

                                                $privacy_management = round($privacy_management, 0) * 1;

                                                $screen_time = round($screen_time, 0) * 1;



                                                $critical_thinking_color = getBgColor($critical_thinking);

                                                $cyber_security_management_color = getBgColor($cyber_security_management);

                                                $cyberbullying_color = getBgColor($cyberbullying);

                                                $digital_citizen_identity_color = getBgColor($digital_citizen_identity);

                                                $digital_empathy_color = getBgColor($digital_empathy);

                                                $digital_footprint_color = getBgColor($digital_footprint);

                                                $privacy_management_color = getBgColor($privacy_management);

                                                $screen_time_color = getBgColor($screen_time);



                                                echo '<tr>';

                                                echo '<td style="vertical-align: middle;">' . $row['SCHOOL'] . '</td>';

                                                echo '<td style="vertical-align: middle;">' . $row['GRADE'] . '</td>';

                                                echo '<td style="vertical-align: middle;">' . $row['COUNT_OF_STUDENT'] . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $critical_thinking_color . ';" class="text-end text-white">' . $critical_thinking . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $cyber_security_management_color . ';" class="text-end text-white">' . $cyber_security_management . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $cyberbullying_color . ';" class="text-end text-white">' . $cyberbullying . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $digital_citizen_identity_color . ';" class="text-end text-white">' . $digital_citizen_identity . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $digital_empathy_color . ';" class="text-end text-white">' . $digital_empathy . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $digital_footprint_color . ';" class="text-end text-white">' . $digital_footprint . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $privacy_management_color . ';" class="text-end text-white">' . $privacy_management . '</td>';

                                                echo '<td style="vertical-align: middle; background-color: ' . $screen_time_color . ';" class="text-end text-white">' . $screen_time . '</td>';

                                                echo '</tr>';
                                            }

                                            ?>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- Vendor js -->

    <script src="assets/js/vendor.min.js"></script>



    <!-- App js -->

    <script src="assets/js/app.min.js"></script>

</body>

</html>