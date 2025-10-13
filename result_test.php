<html>
<head>
    <title>DQ-Smartplus</title>
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
<body style="background-color: white;">
<?php
include 'program/resheader.php';
?>
<br>
<br>
<br>
<br>
    <img src="img/topresult.PNG" width="100%">
    <br>
    <table width="100%">
        <tr>
            <?php
            include 'program/koneksi.php';
            $sql = "SELECT * FROM `account` WHERE ID = " . $_SESSION['ID'];
            $result = $conn->query($sql);
            $ID = $_SESSION['ID'];
            $username = '';
            $school = '';
            $sendmail = 0;
            $lang = isset($_SESSION['lang'])?$_SESSION['lang']:"";
            $lang = $lang==""?"id":"en";
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $username = $row['USERNAME'];
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
$sql = "SELECT CONVERT((SUM(R.VALUE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'";
$result = $conn->query($sql);
$nilaischool = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaischool = $row['TOTAL'];
    }
}
$sql = "SELECT CONVERT((SUM(R.VALUE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND X.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID AND STATE = 'FINISH') AND A.GRADE = (SELECT GRADE FROM account WHERE ID = $ID AND STATE = 'FINISH')";
$result = $conn->query($sql);
$nilaiclass = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaiclass = $row['TOTAL'];
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
                    $tabul = $tabul . "<tr style='border: 1px solid grey; border-collapse: collapse;'>";
                    
                    if($row['TIPE'] == 'Screen Time')
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type1.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK </h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen waktu menatap layar; mengukur kemampuan anda untuk dapat mengatur waktu menatap layar, multitasking dan kemampuan untuk mengontrol diri dalam penggunaan bermacam-macam aktivitas media digital. <br>Skor manajemen waktu menatap layar yang tinggi akan : <br>- Menyeimbangkan realitas fisik dan virtual <br>- Mengendalikan diri dalam penggunaan teknologi digital dan memahami berbagai efek samping dari screen time yang berlebihan, multitasking dan penggunaan media digital yang aktif. <br>- Mampu mengatur waktu dan menetapkan batas penggunaan pribadi tanpa membiarkan penggunaan digital mengambil alih hidup anda.<br><br>Selamat! performa anda dalam kategori Screen Time Management ini sangat baik</td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen waktu menatap layar; mengukur kemampuan anda untuk dapat mengatur waktu menatap layar, multitasking dan kemampuan untuk mengontrol diri dalam penggunaan bermacam-macam aktivitas media digital. <br>Skor manajemen waktu menatap layar yang tinggi akan : <br>- Menyeimbangkan realitas fisik dan virtual <br>- Mengendalikan diri dalam penggunaan teknologi digital dan memahami berbagai efek samping dari screen time yang berlebihan, multitasking dan penggunaan media digital yang aktif. <br>- Mampu mengatur waktu dan menetapkan batas penggunaan pribadi tanpa membiarkan penggunaan digital mengambil alih hidup anda.<br>Anda telah memahami dengan baik ! Prestasi anda dalam kategori Screen TimeManagement ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management waktu menatap layar mengukur kemampuan anda untuk mengelola waktu menatap layar, multi tasking dan keterlibatan dalam berbagai aktifitas media digital dalam mengontrol diri. Skor management waktu menatap layar yang tinggi akan :<br>- Menyeimbangkan realitas fisik dan virtual<br>- Mengendalikan diri dalam penggunaan teknologi digital dan memahami berbagai effek samping dari screen time yang berlebihan, multi tasking dan penggunaanmedia digital yang adiktif.<br>- Mampu mengatur waktu dan menetapkan batas penggunaan pribadi tanpamembiarkan penggunaan digital mengambil alih hidup anda.<br><br>Prestasi anda dalam kategori ini kurang memuaskan dan masih harus dikembangkan agar dapat mengatur manajemen waktu dengan baik.</td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen waktu menatap layar; mengukur kemampuan anda untuk dapat mengatur waktu menatap layar, multitasking dan kemampuan untuk mengontrol diri dalam penggunaan bermacam-macam aktivitas media digital. <br>Skor manajemen waktu menatap layar yang tinggi akan : <br>- Menyeimbangkan realitas fisik dan virtual <br>- Mengendalikan diri dalam penggunaan teknologi digital dan memahami berbagai efek samping dari screen time yang berlebihan, multitasking dan penggunaan media digital yang aktif. <br>- Mampu mengatur waktu dan menetapkan batas penggunaan pribadi tanpa membiarkan penggunaan digital mengambil alih hidup anda.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($row['TIPE'] == 'Privacy Management')
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type2.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management Privacy; mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungi privasi anda dan orang lain. <br>Anda dengan skor Management Privacy yang tinggi akan : <br>- Memiliki pengetahuan dan keterampilan untuk menangani informasi pribadi yang dibagikan secara online dengan bijaksana. <br>- Memiliki pengertian untuk memastikan dan melindungi privasi diri sendiri dan juga orang lain. <br>- Menyadari bahwa privasi adalah hak asasi manusia yang mendasar.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman Privacy Management dengan baik. </td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management Privacy; mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungi privasi anda dan orang lain. <br>Anda dengan skor Management Privacy yang tinggi akan : <br>- Memiliki pengetahuan dan keterampilan untuk menangani informasi pribadi yang dibagikan secara online dengan bijaksana. <br>- Memiliki pengertian untuk memastikan dan melindungi privasi diri sendiri dan juga orang lain. <br>- Menyadari bahwa privasi adalah hak asasi manusia yang mendasar.<br><br>Anda telah memahami dengan baik ! Prestasi anda dalam kategori Privasi Management memuaskan. </td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'> Management Privacy; mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungi privasi anda dan orang lain. <br>Anda dengan skor Management Privacy yang tinggi akan : <br>- Memiliki pengetahuan dan keterampilan untuk menangani informasi pribadi yang dibagikan secara online dengan bijaksana. <br>- Memiliki pengertian untuk memastikan dan melindungi privasi diri sendiri dan juga orang lain. <br>- Menyadari bahwa privasi adalah hak asasi manusia yang mendasar.<br><br>Prestasi anda dalam Privacy Management ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management Privacy; mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungi privasi anda dan orang lain. <br>Anda dengan skor Management Privacy yang tinggi akan : <br>- Memiliki pengetahuan dan keterampilan untuk menangani informasi pribadi yang dibagikan secara online dengan bijaksana. <br>- Memiliki pengertian untuk memastikan dan melindungi privasi diri sendiri dan juga orang lain. <br>- Menyadari bahwa privasi adalah hak asasi manusia yang mendasar.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital. </td>";
                        }
                    }
                    elseif($row['TIPE'] == 'Cyberbullying')
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type3.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menanganinya dengan bijak. <br>Anda dengan skor Cyberbullying yang tinggi akan : <br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab. <br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang. <br>- Mengetahui bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum masalah menjadi tidak terkendali.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Cyberbullying Management dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menanganinya dengan bijak. <br>Anda dengan skor Cyberbullying yang tinggi akan : <br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab. <br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang. <br>- Mengetahui bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum masalah menjadi tidak terkendali.<br><br>Anda telah memahami dengan baik Cyberbullying Management ! Prestasi anda dalam Kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menanganinya dengan bijak. <br>Anda dengan skor Cyberbullying yang tinggi akan : <br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab. <br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang. <br>- Mengetahui bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum masalah menjadi tidak terkendali.<br><br>Prestasi anda dalam Cyberbullying Management kurang memuaskan dan masih harus dikembangkan. </td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menanganinya dengan bijak. <br>Anda dengan skor Cyberbullying yang tinggi akan : <br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab. <br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang. <br>- Mengetahui bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum masalah menjadi tidak terkendali.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($row['TIPE'] == 'Digital Citizen Identity')
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type4.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Citizen Identity; mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Digital Citizen Identity Skill dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Citizen Identity; mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Anda telah memahami dengan baik Digital Citizen Identity Skill ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Citizen Identity; mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Prestasi anda dalam Digital Citizen Identity Skill ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Citizen Identity; mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($row['TIPE'] == 'Digital Footprint')
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type5.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Jejak Digital; mengukur kemampuan anda dalam memahami sifat jejak digital, konsekuensinya pada kehidupan nyata dan bagaimana mengelolanya secara bertanggung jawab. <br>Anda dengan skor Manajemen Jejak Digital yang tinggi akan : <br>- Memahami sifat komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online akan meninggalkan jejak yang disebut dengan jejak digital. <br>- Mewaspadai sifat presisten dari jejak digital dan konsekuensinya dalam kehidupan nyata, termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki keterampilan untuk mengelola jejak digital secara bertanggung jawab. mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Digital Footprint Management dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Jejak Digital; mengukur kemampuan anda dalam memahami sifat jejak digital, konsekuensinya pada kehidupan nyata dan bagaimana mengelolanya secara bertanggung jawab. <br>Anda dengan skor Manajemen Jejak Digital yang tinggi akan : <br>- Memahami sifat komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online akan meninggalkan jejak yang disebut dengan jejak digital. <br>- Mewaspadai sifat presisten dari jejak digital dan konsekuensinya dalam kehidupan nyata, termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki keterampilan untuk mengelola jejak digital secara bertanggung jawab. mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Anda telah memahami dengan baik Digital Footprint Management ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Jejak Digital; mengukur kemampuan anda dalam memahami sifat jejak digital, konsekuensinya pada kehidupan nyata dan bagaimana mengelolanya secara bertanggung jawab. <br>Anda dengan skor Manajemen Jejak Digital yang tinggi akan : <br>- Memahami sifat komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online akan meninggalkan jejak yang disebut dengan jejak digital. <br>- Mewaspadai sifat presisten dari jejak digital dan konsekuensinya dalam kehidupan nyata, termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki keterampilan untuk mengelola jejak digital secara bertanggung jawab. mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Prestasi anda dalam kategori Digital Footprint Management ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Jejak Digital; mengukur kemampuan anda dalam memahami sifat jejak digital, konsekuensinya pada kehidupan nyata dan bagaimana mengelolanya secara bertanggung jawab. <br>Anda dengan skor Manajemen Jejak Digital yang tinggi akan : <br>- Memahami sifat komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online akan meninggalkan jejak yang disebut dengan jejak digital. <br>- Mewaspadai sifat presisten dari jejak digital dan konsekuensinya dalam kehidupan nyata, termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki keterampilan untuk mengelola jejak digital secara bertanggung jawab. mengukur kemampuan anda untuk membangun dan mengelola identitas yang sehat dan berintegritas, baik secara online ataupun offline. <br>Anda dengan skor Digital Citizen Identity yang tinggi akan : <br>- Memiliki pemahaman yang sangat baik mengenai cara kerja dunia digital dan dapat menggunakan teknologi dengan percaya diri yang kuat. <br>- Memiliki pengetahuan dan keterampilan untuk membangun serta mengelola identitas diri yang sehat. <br>- Menyadari bagaimana menjadi warga negara global yang bertanggung jawab diruang digital.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($row['TIPE'] == 'Cyber Security Management')
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type6.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Keamanan Cyber; mengukur kemampuan anda dalam melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk mengelola berbagai serangan di dunia maya, yaitu: SPAM / SCAM /Phishing. <br>Anda dengan skor manajemen keamanan cyber yang tinggi akan : <br>- Memiliki kemampuan untuk dapat mengenali serta melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti: Spam/Scam dan Phishing. <br>- Memiliki keterampilan praktis seperti membuat sandi yang kuat dan mengetahui cara melindungi diri.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Cyber Security Management dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Keamanan Cyber; mengukur kemampuan anda dalam melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk mengelola berbagai serangan di dunia maya, yaitu: SPAM / SCAM /Phishing. <br>Anda dengan skor manajemen keamanan cyber yang tinggi akan : <br>- Memiliki kemampuan untuk dapat mengenali serta melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti: Spam/Scam dan Phishing. <br>- Memiliki keterampilan praktis seperti membuat sandi yang kuat dan mengetahui cara melindungi diri.<br><br>Anda telah memahami dengan baik Cyber Security Management ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Keamanan Cyber; mengukur kemampuan anda dalam melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk mengelola berbagai serangan di dunia maya, yaitu: SPAM / SCAM /Phishing. <br>Anda dengan skor manajemen keamanan cyber yang tinggi akan : <br>- Memiliki kemampuan untuk dapat mengenali serta melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti: Spam/Scam dan Phishing. <br>- Memiliki keterampilan praktis seperti membuat sandi yang kuat dan mengetahui cara melindungi diri.<br><br>Prestasi anda dalam kategori Cyber Security Management ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Manajemen Keamanan Cyber; mengukur kemampuan anda dalam melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk mengelola berbagai serangan di dunia maya, yaitu: SPAM / SCAM /Phishing. <br>Anda dengan skor manajemen keamanan cyber yang tinggi akan : <br>- Memiliki kemampuan untuk dapat mengenali serta melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti: Spam/Scam dan Phishing. <br>- Memiliki keterampilan praktis seperti membuat sandi yang kuat dan mengetahui cara melindungi diri.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($row['TIPE'] == 'Critical Thinking')
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type7.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Berpikir Kritis; mengukur kemampuan anda untuk membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan mana kontak online yang dapat dipercaya dan dipertanyakan. <br>Anda dengan skor berpikir kritis tinggi akan: <br>- Memiliki pengetahuan serta keterampilan untuk mengevaluasi informasi, konten dan juga kontak di internet dengan bijaksana. <br>- Memahami efek berbahaya dari informasi palsu, konten kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online. <br>- Menunjukkan pemikiran yang kritis saat membedakan antara informasi yang benar atau salah, konten yang baik dan berbahaya serta kontak online yang dipercaya atau dipertanyakan.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Critical Thinking Skill dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Berpikir Kritis; mengukur kemampuan anda untuk membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan mana kontak online yang dapat dipercaya dan dipertanyakan. <br>Anda dengan skor berpikir kritis tinggi akan: <br>- Memiliki pengetahuan serta keterampilan untuk mengevaluasi informasi, konten dan juga kontak di internet dengan bijaksana. <br>- Memahami efek berbahaya dari informasi palsu, konten kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online. <br>- Menunjukkan pemikiran yang kritis saat membedakan antara informasi yang benar atau salah, konten yang baik dan berbahaya serta kontak online yang dipercaya atau dipertanyakan.<br><br>Anda telah memahami dengan baik Critical Tihinking Skill ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Berpikir Kritis; mengukur kemampuan anda untuk membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan mana kontak online yang dapat dipercaya dan dipertanyakan. <br>Anda dengan skor berpikir kritis tinggi akan: <br>- Memiliki pengetahuan serta keterampilan untuk mengevaluasi informasi, konten dan juga kontak di internet dengan bijaksana. <br>- Memahami efek berbahaya dari informasi palsu, konten kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online. <br>- Menunjukkan pemikiran yang kritis saat membedakan antara informasi yang benar atau salah, konten yang baik dan berbahaya serta kontak online yang dipercaya atau dipertanyakan.<br><br>Prestasi anda dalam kategori Critical Thinking Skill ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Berpikir Kritis; mengukur kemampuan anda untuk membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan mana kontak online yang dapat dipercaya dan dipertanyakan. <br>Anda dengan skor berpikir kritis tinggi akan: <br>- Memiliki pengetahuan serta keterampilan untuk mengevaluasi informasi, konten dan juga kontak di internet dengan bijaksana. <br>- Memahami efek berbahaya dari informasi palsu, konten kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online. <br>- Menunjukkan pemikiran yang kritis saat membedakan antara informasi yang benar atau salah, konten yang baik dan berbahaya serta kontak online yang dipercaya atau dipertanyakan.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    else
                    {
                        $tabul = $tabul . "<td style='width:5%; background-color: white;'><center><img width='220' src='res/type8.JPG'></center></td>";
                        if($row['TOTAL'] > 115)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #260e83;'><center><h2>Excellent<br>SANGAT BAIK</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Empathy; mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri serta orang lain secara online. <br>Anda dengan skor Digital Empathy yang tinggi akan : <br>- Memiliki kepekaan terhadap kebutuhan dan perasaan diri sendiri serta orang lain saat online bahkan tanpa perlu berinteraksi tatap muka. <br>- Memiliki kemampuan untuk membangun hubungan yang baik dengan orang tua,guru dan teman secara online maupun offline. <br>- Menolak pola pikir yang menghakimi secara online dan memiliki keberanian untuk bersuara bagi mereka yang membutuhkan bantuan.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Digital Empathy Skill dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 100)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #f58a0a;'><center><h2>Satisfactory<BR>MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Empathy; mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri serta orang lain secara online. <br>Anda dengan skor Digital Empathy yang tinggi akan : <br>- Memiliki kepekaan terhadap kebutuhan dan perasaan diri sendiri serta orang lain saat online bahkan tanpa perlu berinteraksi tatap muka. <br>- Memiliki kemampuan untuk membangun hubungan yang baik dengan orang tua,guru dan teman secara online maupun offline. <br>- Menolak pola pikir yang menghakimi secara online dan memiliki keberanian untuk bersuara bagi mereka yang membutuhkan bantuan.<br><br>Anda telah memahami dengan baik Digital Empathy Skill ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #ed2207;'><center><h2>Less than Satisfactory<BR>KURANG MEMUASKAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Empathy; mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri serta orang lain secara online. <br>Anda dengan skor Digital Empathy yang tinggi akan : <br>- Memiliki kepekaan terhadap kebutuhan dan perasaan diri sendiri serta orang lain saat online bahkan tanpa perlu berinteraksi tatap muka. <br>- Memiliki kemampuan untuk membangun hubungan yang baik dengan orang tua,guru dan teman secara online maupun offline. <br>- Menolak pola pikir yang menghakimi secara online dan memiliki keberanian untuk bersuara bagi mereka yang membutuhkan bantuan.<br><br>Prestasi anda dalam kategori Digital Empathy Skill ini kurang memuaskan dan masih harus dikembangkan. </td>";
                        }
                        else
                        {
                            $tabul = $tabul . "<td style='width:20%; background-color: #c40010;'><center><h2>Requires Attention<BR>MEMBUTUHKAN PERHATIAN</h2></center></td>";
                            $tabul = $tabul . "<td style='width:60%; background-color: White;'>Digital Empathy; mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri serta orang lain secara online. <br>Anda dengan skor Digital Empathy yang tinggi akan : <br>- Memiliki kepekaan terhadap kebutuhan dan perasaan diri sendiri serta orang lain saat online bahkan tanpa perlu berinteraksi tatap muka. <br>- Memiliki kemampuan untuk membangun hubungan yang baik dengan orang tua,guru dan teman secara online maupun offline. <br>- Menolak pola pikir yang menghakimi secara online dan memiliki keberanian untuk bersuara bagi mereka yang membutuhkan bantuan.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    $tabul = $tabul . "<td style='width: 5%; background-color: grey;'><br></td>";
                    $tabul = $tabul . "</tr>";
                    $x++;
                }
            } else {
                echo "0 results";
            }
            $national = '';
            $sql = "SELECT Q.TIPE, CONVERT((SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH' GROUP BY Q.TIPE ";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $x = 0;
                while($row = $result->fetch_assoc()) {
                    if ($x == 0)
                    {
                        $national = $row['TOTAL'];
                    }
                    else
                    {
                        $national = $national . ', ' . $row['TOTAL'];
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
            $sql = "SELECT Q.TIPE, CONVERT((SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND X.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND A.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH' GROUP BY Q.TIPE";
            $querglob = $sql;
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $x = 0;
                while($row = $result->fetch_assoc()) {
                    if ($x == 0)
                    {
                        $global = $row['TOTAL'];
                    }
                    else
                    {
                        $global = $global . ', ' . $row['TOTAL'];
                    }
                    $x++;
                }
            }
            else
            {
                $global = '0, 0, 0, 0, 0, 0, 0, 0';
            }
            ?>
            <td style="width: 10%"><br></td>
            <td style="width: 40%; text-align: right;"><h3>Date of Score Card Generation : <font style="color: #0b93d5;"><?php echo $generation;?></font></h3></td>
            <td style="width: 20%; text-align: right;"><h3>Username : <font style="color: #0b93d5;"><?php echo $username;?></font></h3></td>
            <td style="width: 30%; text-align: right;"><h3>School : <font style="color: #0b93d5;"><?php echo $school;?></font></h3></td>
        </tr>
    </table>
    <br>
    <br>
    <table width="100%">
        <tr>
            <?php
                $totnil = $totnil / 8;
                $nilaischool = $nilaischool / 8;
                $nilaiclass = $nilaiclass / 8;
            ?>
            <td style="width: 15%; background-color: grey"><center><h2 style="color: white">Your Total <br>DQ Score</h2><br><h1 style="color: orange"><?php echo intval($totnil); ?></h1></center></td>
            <td rowspan="3" style="width: 70%"><canvas style="width: 1400px;" id="chart"></canvas> </td>
            <td rowspan="3" style="width: 15%"><img src="img/isitabelkanan.PNG" width="100%" height="100%"></td>
        </tr>
        <tr>
            <td><center><h2 style="color: darkgray;">School<br>Average<br>DQ Score</h2><h1 style="color: grey"><?php echo intval($nilaischool); ?></h1></center></td>
        </tr>
        <tr>
            <td><center><h2 style="color: darkgray;">Class Average<br>DQ Score</h2><h1 style="color: grey"><?php echo intval($nilaiclass); ?></h1></center></td>
        </tr>
    </table>
    <br>
    <br>
    <table width="100%">
        <tr>
            <td style="width: 5%; background-color: grey; border: 1px solid white; border-collapse: collapse;" rowspan="10"><br></td>
            <td style="width:10%; background-color: grey; border: 1px solid white; border-collapse: collapse;" colspan="4"><br></td>
        </tr>
        <tr>
            <td style="width:10%; background-color: grey; color: White; border: 1px solid white; border-collapse: collapse;"><center><h3 style="color: white">Type</h3></center></td>
            <td style="width:20%; background-color: grey; color: White; border: 1px solid white; border-collapse: collapse;"><center><h3 style="color: white">Level</h3></center></td>
            <td style="width:60%; background-color: grey; color: White; border: 1px solid white; border-collapse: collapse;"><center><h3 style="color: white">Meaning</h3></center></td>
            <td style="width: 5%; background-color: grey; border: 1px solid white; border-collapse: collapse;"><br></td>
        </tr>
        <?php echo $tabul; ?>
        <tr>
            <td style="width: 5%; background-color: grey; border: 1px solid white; border-collapse: collapse;" rowspan="10"><br></td>
            <td style="width:15%; background-color: grey; border: 1px solid white; border-collapse: collapse;" colspan="4"><br></td>
        </tr>
    </table>
    <br>
    <br>
    <table width="100%">
        <tr>
            <td style="width:5%"><br></td>
            <td style="width:90%">
                <img src="img/result2.JPG" width="100%">
                </td>
    <td style="width:5%"><br></td>
    </tr>
    </table>
    

    <a href="https://chartjs-plugin-datalabels.netlify.com/" target="_blank">chartjs-plugin-datalabels</a>
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        var data = {
            labels: ["Privacy Management", "Critical Thinking", "Digital Footprint Management", "Digital Empathy", "Cyber Security Management", "Cyberbullying Management", "Screen Time Management", "Digital Citizen Identity"],
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