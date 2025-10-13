<?php
include 'program/koneksi.php';
include 'lang.php';
$lang = isset($_SESSION['lang'])?$_SESSION['lang']:"id";
$sql = "SELECT * FROM `account` WHERE ID = " . $_SESSION['ID'];
$result = $conn->query($sql);
$ID = $_SESSION['ID'];
$usernameSession = '';
$school = '';
$sendmail = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $usernameSession = $row['USERNAME'];
        $school = $row['SCHOOL'];
        $sendmail = $row['SENDMAIL'];
    }
} else {
    echo "0 results";
}
$sql = "SELECT DAY(MAX(ACTIVITY_ON)) AS TANGGAL, MONTHNAME(MAX(ACTIVITY_ON)) AS BULAN, YEAR(MAX(ACTIVITY_ON)) AS TAHUN FROM `RESULT` WHERE USERID = " . $_SESSION['ID'];
$result = $conn->query($sql);
$generation = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $generation = $row['TANGGAL'] . ' ' . $row['BULAN'] . ' ' . $row['TAHUN'];
    }
} else {
    echo "0 results";
}
$sql = "SELECT SUM(VALUE) AS TOTAL FROM RESULT WHERE USERID = $ID";
$result = $conn->query($sql);
$nilaiself = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaiself = $row['TOTAL'];
    }
}
//$sql = "SELECT CONVERT((SUM(R.VALUE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'";
$sql = "SELECT (SUM(R.VALUE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH') AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'";
$result = $conn->query($sql);
$nilaischool = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaischool = (int)$row['TOTAL'];
    }
}
//$sql = "SELECT CONVERT((SUM(R.VALUE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND X.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID AND STATE = 'FINISH') AND A.GRADE = (SELECT GRADE FROM account WHERE ID = $ID AND STATE = 'FINISH')";
$sql = "SELECT (SUM(R.VALUE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND X.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH') AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID AND STATE = 'FINISH') AND A.GRADE = (SELECT GRADE FROM account WHERE ID = $ID AND STATE = 'FINISH')";
$result = $conn->query($sql);
$nilaiclass = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaiclass = (int)$row['TOTAL'];
    }
} 
$sql = "SELECT TIPE, SUM(VALUE) AS TOTAL FROM RESULT AS A LEFT JOIN QUESTION AS B ON A.QUESTION = B.ID LEFT JOIN CATEGORY AS C ON B.TIPE = C.KATEGORI WHERE A.USERID = $ID GROUP BY B.TIPE ORDER BY C.ID";
$result = $conn->query($sql);
$nilai = '';
$totnil = 0;
$tabul = '';
if ($result->num_rows > 0) {
    // output data of each row
    $x = 0;
    while($row = $result->fetch_assoc()) {
        if ($x == 0)
        {
            $nilai = $row['TOTAL'];
            $totnil = $row['TOTAL'];
        }
        else
        {
            $nilai = $nilai . ', ' . $row['TOTAL'];
            $totnil = $totnil + $row['TOTAL'];
        }
        $isx = $x + 1;
        $tabul = $tabul . "<tr>";
        
        if($row['TIPE'] == 'Screen Time')
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type1.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['screen_time_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['screen_time_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['screen_time_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['screen_time_d']."</td>";
            }
        }
        elseif($row['TIPE'] == 'Privacy Management')
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type2.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['privacy_management_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['privacy_management_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['privacy_management_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['privacy_management_d']."</td>";
            }
        }
        elseif($row['TIPE'] == 'Cyberbullying')
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type3.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyberbullying_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyberbullying_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyberbullying_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyberbullying_d']."</td>";
            }
        }
        elseif($row['TIPE'] == 'Digital Citizen Identity')
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type4.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_citizen_identity_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_citizen_identity_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_citizen_identity_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_citizen_identity_d']."</td>";
            }
        }
        elseif($row['TIPE'] == 'Digital Footprint')
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type5.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_footprint_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_footprint_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_footprint_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_footprint_d']."</td>";
            }
        }
        elseif($row['TIPE'] == 'Cyber Security Management')
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type6.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyber_security_management_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyber_security_management_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyber_security_management_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['cyber_security_management_d']."</td>";
            }
        }
        elseif($row['TIPE'] == 'Critical Thinking')
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type7.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['critical_thinking_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['critical_thinking_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['critical_thinking_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['critical_thinking_d']."</td>";
            }
        }
        else
        {
            $tabul = $tabul . "<td><center><img width='220' src='res/type8.JPG'></center></td>";
            if($row['TOTAL'] > 115)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2 style='color: white;'>".$arrLang[$lang]['excellent']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_empathy_a']."</td>";
            }
            elseif($row['TOTAL'] >= 100)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2 style='color: white;'>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_empathy_b']."</td>";
            }
            elseif($row['TOTAL'] >= 85)
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2 style='color: white;'>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_empathy_c']."</td>";
            }
            else
            {
                $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2 style='color: white;'>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                $tabul = $tabul . "<td style='width:60%; background-color: White;'>".$arrLang[$lang]['digital_empathy_d']."</td>";
            }
        }
        $tabul = $tabul . "</tr>";
        $x++;
    }
}

$national = '';
//$sql = "SELECT Q.TIPE, CONVERT((SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH' GROUP BY Q.TIPE ";
$sql = "SELECT Q.TIPE, (SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH') AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH' GROUP BY Q.TIPE ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $x = 0;
    while($row = $result->fetch_assoc()) {
        if ($x == 0)
        {
            $national = (int)$row['TOTAL'];
        }
        else
        {
            $national = $national . ', ' . (int)$row['TOTAL'];
        }
        $x++;
    }
}
else
{
    $national = '0, 0, 0, 0, 0, 0, 0, 0';
}
$global = '';
$querglob = '';
//$sql = "SELECT Q.TIPE, CONVERT((SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND X.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND A.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH' GROUP BY Q.TIPE";
$sql = "SELECT Q.TIPE, (SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND X.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH') AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND A.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH' GROUP BY Q.TIPE";
$querglob = $sql;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $x = 0;
    while($row = $result->fetch_assoc()) {
        if ($x == 0)
        {
            $global = (int)$row['TOTAL'];
        }
        else
        {
            $global = $global . ', ' . (int)$row['TOTAL'];
        }
        $x++;
    }
}
else
{
    $global = '0, 0, 0, 0, 0, 0, 0, 0';
}
?>

<html>
<head>
    <title>DQ-Smartplus</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link rel="stylesheet" href="assets/bootstrap5/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" type="text/css" />
    <script src="assets/jquery/jquery.min.js"></script>
    <script src="assets/bootstrap5/js/bootstrap.bundle.min.js"></script>
    <style>
        .bg-dqnavbar {
            background-color: #38414a !important;
        }
        body {
            background-color: #fafaf9 !important;
        }
    </style>
</head>
<body>
    <?php 
        include 'program/resheadernew.php';

        $totnil = $totnil / 8;
        $nilaischool = $nilaischool / 8;
        $nilaiclass = $nilaiclass / 8;
    ?>

    <div class="container">
        <img src="img/topresult.PNG" class="img-fluid mb-4" />

        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <h5><?php echo $lang=="en"?"Date of Score Card Generation":"Tanggal Pembuatan Kartu Skor"; ?></h5>
                <h5 style="color: #0b93d5;"><?php echo $generation; ?></h5>
            </div>

            <div class="col-md-4 text-center">
                <h5><?php echo $lang=="en"?"Username":"Nama Pengguna"; ?></h5>
                <h5 style="color: #0b93d5;"><?php echo $usernameSession; ?>1</h5>
            </div>

            <div class="col-md-4 text-center">
                <h5><?php echo $lang=="en"?"School":"Nama Sekolah"; ?></h5>
                <h5 style="color: #0b93d5;"><?php echo $school; ?>1</h5>
            </div>
        </div>

        <div class="row d-flex">
            <div class="col-md-2 align-self-center">
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-4 mb-4 text-center bg-light rounded-3 border shadow-sm">
                            <h5>Your Total<br/>DQ Score</h5>
                            <h5 style="color: orange;"><?php echo intval($totnil); ?></h5>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="p-4 mb-4 text-center bg-light rounded-3 border shadow-sm">
                            <h5>School Average DQ Score</h5>
                            <h5 style="color: orange;"><?php echo intval($nilaischool); ?></h5>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="p-4 mb-4 text-center bg-light rounded-3 border shadow-sm">
                            <h5>Class Average DQ Score</h5>
                            <h5 style="color: orange;"><?php echo intval($nilaiclass); ?></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 text-center">
                <div class="p-4 mb-4 text-center bg-light rounded-3 border shadow-sm">
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <table class="table table-bordered table-sm table-hover table-striped">
                <thead>
                    <tr>
                        <th class="text-center">Type</th>
                        <th class="text-center">Level</th>
                        <th class="text-center">Meaning</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $tabul; ?>
                </tbody>
            </table>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="p-4 mb-4 bg-light rounded-3 border shadow-sm">
                <div class="col-md-12">
                    <?php
                        if($lang=="id") {
                            echo '<img src="img/result-id.webp?v=0.0.1" class="img-fluid rounded mx-auto d-block">';
                        } else {
                            echo '<img src="img/result-en.webp?v=0.0.1" class="img-fluid rounded mx-auto d-block">';
                        }
                        
                        //<img src="img/result2.JPG" width="100%">
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        var data = {
            labels: [
                "Privacy Management", 
                "Critical Thinking", 
                "Digital Footprint Management", 
                "Digital Empathy", "Cyber Security Management", "Cyberbullying Management", "Screen Time Management", "Digital Citizen Identity"],
            datasets: [{
                label: "You",
                fill: false,
                    borderDash: [10,5],
                borderColor: "blue",
                data: [<?php echo $nilai;?>]
            },
                {

                    label: "School Average",
                    borderColor: "orange",
                    fill: false,
                    data: [<?php echo $national;?>]
                },
                {
                    label: "Class Average",
                    borderColor: "red",
                    fill: false,
                    data: [<?php echo $global;?>]
                }]
        };

        var options = {
            responsive: true,
            tooltips: false,
            title: {
                text: '',
                display: true,
                position: `bottom`,
            },
            plugins: {
                datalabels: {
                    opacity: 20,
                    textAlign: 'left',
                    borderColor: function(context) {
				        return context.dataset.borderColor;
			        },
                    borderWidth: 3,
                    borderRadius: 100,
                    color: function(context) {
				        return context.dataset.borderColor;
			        },
                    backgroundColor: 'white',
                    font: {
                        weight: 'bolder',
                        size: 12,
                        lineHeight: 1,
                        color: 'red',
                        opacity: 100
                    },
                    padding: {
                        top: 5
                    },
                    listeners: {
                        enter: function(context) {
                            context.hovered = true;
                            return true;
                        },
                        leave: function(context) {
                            context.hovered = false;
                            return true;
                        }
                    },
                    formatter: function(value, context) {
                        return context.value;
                    }
                }
            },
            scale: {
                angleLines: {
                    display: true
                },
                pointLabels:{
                    fontSize: 15,
                    fontColor: 'black',
                    fontStyle: 'bold',
                    callback: function(value, index, values) {
                        return value;
                    }
                },
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 100,
                    stepSize: 25,
                    maxTicksLimit: 11,
                    display: false,
                }
            },
            legend: {
                labels: {
                    padding: 10,
                    fontSize: 14,
                    lineHeight: 30,
                },
            },
        };
        
        var myChart = new Chart(document.getElementById("chart"), {
            type: 'radar',
            data: data,
            options: options
            });
            
            var canvas = document.getElementById('chart');
            
            function senddata() {
            
            var dataURL = canvas.toDataURL();
            $.ajax({
                type: "POST",
                url: "program/saveimage.php",
                data: {
                    gambar: dataURL
                }
            }).done(function(o) {
                $.ajax({
                type: "POST",
                url: "mail3.php"
            }).done(function(o) {
                alert('Mail has been sent.');
            });
            });
        }
        
        $(window).on('load', function() {
            <?php
                echo 'console.log("$querglob");';
            ?>
        });
    </script>
</body>
</html>