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

</head>
<body class="loading" data-layout-mode="horizontal" data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>
    <div id="wrapper">
        <?php 
            include 'program/header.php'; 
            include 'program/koneksi.php';
            
            $sqlSchool = "SELECT DISTINCT SCHOOL FROM `account` WHERE STATE ='FINISH' ORDER BY SCHOOL";
            $resultSchool = $conn->query($sqlSchool);
            
            $param_school = isset($_GET['school'])?$_GET['school']:"";
            $from_date = date("Y-m-d", strtotime("-6 months"));
            $to_date = date("Y-m-d");
            
            if (isset($_GET['fromDate'])) {
                if($_GET['fromDate'] != "") {
                    $from_date = $_GET['fromDate'];
                }
            }
            
            if (isset($_GET['toDate'])) {
                if($_GET['toDate'] != "") {
                    $to_date = $_GET['toDate'];
                }
            }

            $sql = "
                SELECT
                    A.SCHOOL,
                    IFNULL(A.GRADE, 'Tidak Ada Kelas') AS GRADE,
                    (SELECT COUNT(ID) FROM account AS a1 WHERE a1.SCHOOL = A.SCHOOL AND a1.STATE = 'FINISH') AS COUNT_OF_STUDENT,
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
            
            if($param_school != "") {
                $sql .= " AND A.SCHOOL = '".$param_school."'";
            }
            
            if ($from_date != "") {
                $sql .= " AND R.ACTIVITY_ON >= '".$from_date." 00:00:00'";
            }
            
            if ($to_date != "") {
                $sql .= " AND R.ACTIVITY_ON <= '".$to_date." 23:59:00'";
            }
            
            $sql .= " GROUP BY
                    A.SCHOOL,
                    A.GRADE
                ORDER BY
                    A.SCHOOL,
                    A.GRADE
            ";
            $result = $conn->query($sql);
            
            function getBgColor($nilai) {
                $warna = "";
                if($nilai > 115) {
                    $warna = "#260e83";
                } elseif($nilai >= 100) {
                    $warna = "#f58a0a";
                } elseif($nilai >= 85) {
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
                                                        while($row = $resultSchool->fetch_assoc()) 
                                                        {
                                                            if($param_school == $row['SCHOOL']) {
                                                                echo '<option selected value="'.$row['SCHOOL'].'">'.$row['SCHOOL'].'</option>';
                                                            } else {
                                                                echo '<option value="'.$row['SCHOOL'].'">'.$row['SCHOOL'].'</option>';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label for="fromDate" class="col-md-2 col-form-label col-form-label-sm">Result Date</label>
                                            <div class="col-md-2">
                                                <input type="date" class="form-control" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
                                            </div>
                                            <div class="col-md-2">
                                                <input type="date" class="form-control" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-2 offset-md-2">
                                                <button class="btn btn-primary" type="submit">Search</button>
                                                <button class="btn btn-success" type="button" onclick="exportData()">Export</button>
                                            </div>
                                        </div>
                                        
                                    </form>
                                    
                                    <table class="table table-bordered mb-0" width="100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="align-middle">School</th>
                                                <th class="align-middle">Grade</th>
                                                <th class="align-middle">Total Students</th>
                                                <th class="align-middle" width="8%">Critical Thinking</th>
                                                <th class="align-middle" width="8%">Cyber Security Managemen</th>
                                                <th class="align-middle" width="8%">Cyberbullying</th>
                                                <th class="align-middle" width="8%">Digital Citizen Identity</th>
                                                <th class="align-middle" width="8%">Digital Empathy</th>
                                                <th class="align-middle" width="8%">Digital Footprint</th>
                                                <th class="align-middle" width="8%">Privacy Management</th>
                                                <th class="align-middle" width="8%">Screen Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while($row = $result->fetch_assoc()) 
                                            {
                                                $critical_thinking = $row['critical_thinking'] / $row['COUNT_OF_STUDENT'];
                                                $cyber_security_management = $row['cyber_security_management'] / $row['COUNT_OF_STUDENT'];
                                                $cyberbullying = $row['cyberbullying'] / $row['COUNT_OF_STUDENT'];
                                                $digital_citizen_identity = $row['digital_citizen_identity'] / $row['COUNT_OF_STUDENT'];
                                                $digital_empathy = $row['digital_empathy'] / $row['COUNT_OF_STUDENT'];
                                                $digital_footprint = $row['digital_footprint'] / $row['COUNT_OF_STUDENT'];
                                                $privacy_management = $row['privacy_management'] / $row['COUNT_OF_STUDENT'];
                                                $screen_time = $row['screen_time'] / $row['COUNT_OF_STUDENT'];
                                                
                                                $critical_thinking_color = "";
                                                if($critical_thinking > 115) {
                                                    $critical_thinking_color = "#260e83";
                                                } elseif($critical_thinking >= 100) {
                                                    $critical_thinking_color = "#f58a0a";
                                                } elseif($critical_thinking >= 85) {
                                                    $critical_thinking_color = "#ed2207";
                                                } else {
                                                    $critical_thinking_color = "#c40010";
                                                }
                                                
                                                $critical_thinking = round($critical_thinking, 2) * 1;
                                                $cyber_security_management = round($cyber_security_management, 2) * 1;
                                                $cyberbullying = round($cyberbullying, 2) * 1;
                                                $digital_citizen_identity = round($digital_citizen_identity, 2) * 1;
                                                $digital_empathy = round($digital_empathy, 2) * 1;
                                                $digital_footprint = round($digital_footprint, 2) * 1;
                                                $privacy_management = round($privacy_management, 2) * 1;
                                                $screen_time = round($screen_time, 2) * 1;
                                                
                                                $critical_thinking_color = getBgColor($critical_thinking);
                                                $cyber_security_management_color = getBgColor($cyber_security_management);
                                                $cyberbullying_color = getBgColor($cyberbullying);
                                                $digital_citizen_identity_color = getBgColor($digital_citizen_identity);
                                                $digital_empathy_color = getBgColor($digital_empathy);
                                                $digital_footprint_color = getBgColor($digital_footprint);
                                                $privacy_management_color = getBgColor($privacy_management);
                                                $screen_time_color = getBgColor($screen_time);
                                                
                                                echo '<tr>';
                                                echo '<td style="vertical-align: middle;">'.$row['SCHOOL'].'</td>';
                                                echo '<td style="vertical-align: middle;">'.$row['GRADE'].'</td>';
                                                echo '<td style="vertical-align: middle;">'.$row['COUNT_OF_STUDENT'].'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$critical_thinking_color.';" class="text-end text-white">'.$critical_thinking.'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$cyber_security_management_color.';" class="text-end text-white">'.$cyber_security_management.'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$cyberbullying_color.';" class="text-end text-white">'.$cyberbullying.'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$digital_citizen_identity_color.';" class="text-end text-white">'.$digital_citizen_identity.'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$digital_empathy_color.';" class="text-end text-white">'.$digital_empathy.'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$digital_footprint_color.';" class="text-end text-white">'.$digital_footprint.'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$privacy_management_color.';" class="text-end text-white">'.$privacy_management.'</td>';
                                                echo '<td style="vertical-align: middle; background-color: '.$screen_time_color.';" class="text-end text-white">'.$screen_time.'</td>';
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
    
    <script>
        function exportData()
        {
            var school = $("#school").val();
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();
            
            var exportUrl = "report_excel.php?school=" + school;
            exportUrl = exportUrl + "&fromDate=" + fromDate;
            exportUrl = exportUrl + "&toDate=" + toDate;
            
            window.open(exportUrl, 'blank');
        }
    </script>
</body>
</html>