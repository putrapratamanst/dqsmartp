<?php
session_start();
$br = 1;
$mails = '';
$mails = $mails . "<html><head> <title>DQ-Smartplus</title> <link rel=\"shortcut icon\" href=\"assets/images/favicon.ico\"> <link href=\"assets/css/config/default/bootstrap.min.css\" rel=\"stylesheet\" type=\"text/css\" id=\"bs-default-stylesheet\"/> <link href=\"assets/css/config/default/app.min.css\" rel=\"stylesheet\" type=\"text/css\" id=\"app-default-stylesheet\"/> <link href=\"assets/css/config/default/bootstrap-dark.min.css\" rel=\"stylesheet\" type=\"text/css\" id=\"bs-dark-stylesheet\" disabled=\"disabled\"/> <link href=\"assets/css/config/default/app-dark.min.css\" rel=\"stylesheet\" type=\"text/css\" id=\"app-dark-stylesheet\" disabled=\"disabled\"/> <link href=\"assets/css/icons.min.css\" rel=\"stylesheet\" type=\"text/css\"/> <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\" type=\"text/javascript\"></script></head><body style=\"background-color: white;\">";
$mails = $mails . "<img src=\"http://dq-smartplus.com/img/top-result.jpeg\" width=\"100%\"> <br><table width=\"100%\"> <tr>";
include 'program/koneksi.php';
include 'lang.php';
$lang = isset($_SESSION['lang'])?$_SESSION['lang']:"id";
$sql = "UPDATE account SET SENDMAIL = 1 WHERE ID = " . $_SESSION['ID'];
if ($conn->query($sql) === TRUE) {
    
}
$sql = "SELECT * FROM `account` WHERE ID = " . $_SESSION['ID'];
$result = $conn->query($sql);
$ID = $_SESSION['ID'];
$username = '';
$school = '';
$fullname = '';
$email = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $username = $row['USERNAME'];
        $school = $row['SCHOOL'];
        $email = $row['EMAIL'];
        $fullname = $row['FULLNAME'];
    }
} else {
    $mails = $mails . "0 results";
}
$address = 'chart/' . $_SESSION['ID'] . 's.png';
$imagex = base64_encode(file_get_contents($address));
//$haw = "<img src=\"http://dq-smartplus.com/" . $_SESSION['gambar'] . "\" width='100%' height='100%'>";
$image = "<img style='width: 700px; height:350px;' src=\"data:image/png;base64, $imagex\"/>";
$address2 = 'img/tabelkanan.PNG';
$imagex2 = base64_encode(file_get_contents($address2));
//<img src=\"http://dq-smartplus.com/img/isitabelkanan.jpeg\" width=\"100%\" height=\"100%\">
$image2 = "<center><img style='width: 100px; height: 350px;' src=\"data:image/png;base64, $imagex2\"/></center>";
$sql = "SELECT DAY(MAX(ACTIVITY_ON)) AS TANGGAL, MONTHNAME(MAX(ACTIVITY_ON)) AS BULAN, YEAR(MAX(ACTIVITY_ON)) AS TAHUN FROM `RESULT` WHERE USERID = " . $_SESSION['ID'];
$result = $conn->query($sql);
$generation = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $generation = $row['TANGGAL'] . ' ' . $row['BULAN'] . ' ' . $row['TAHUN'];
    }
} else {
    $mails = $mails . "0 results";
}
$sql = "SELECT TIPE, SUM(VALUE) AS TOTAL FROM RESULT AS A LEFT JOIN QUESTION AS B ON A.QUESTION = B.ID LEFT JOIN CATEGORY AS C ON B.TIPE = C.KATEGORI WHERE A.USERID = $ID GROUP BY B.TIPE ORDER BY C.ID";
$result = $conn->query($sql);
$nilai = '';
if ($result->num_rows > 0) {
    // output data of each row
    $x = 0;
    while($row = $result->fetch_assoc()) {
        if ($x == 0)
        {
            $nilai = $row['TOTAL'];
        }
        else
        {
            $nilai = $nilai . ', ' . $row['TOTAL'];
        }
        $x++;
    }
} else {
    $mails = $mails . "0 results";
}
$sql = "SELECT SUM(VALUE) AS TOTAL FROM RESULT WHERE USERID = $ID";
$result = $conn->query($sql);
$nilaiself = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaiself = intval($row['TOTAL'] / 8);
    }
}
$sql = "SELECT IFNULL(CONVERT(SUM(R.VALUE)/(SELECT COUNT(ID) FROM account as A WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID)), INT), 0) AS TOTAL FROM RESULT AS R LEFT JOIN account as A ON R.USERID = A.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID)";
$result = $conn->query($sql);
$nilaischool = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaischool = intval($row['TOTAL'] / 8);
    }
}
$sql = "SELECT IFNULL(CONVERT(SUM(R.VALUE)/(SELECT COUNT(ID) FROM account as A WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID)), INT), 0) AS TOTAL FROM RESULT AS R LEFT JOIN account as A ON R.USERID = A.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND A.GRADE = (SELECT SCHOOL FROM account WHERE ID = $ID)";
$result = $conn->query($sql);
$nilaiclass = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nilaiclass = intval($row['TOTAL'] / 8);
    }
} else {
    $mails = $mails . "0 results";
}
$mails = $mails . "<td style=\"width: 40%; text-align: right;\"><h3>Date of Score Card Generation : <font style=\"color: #0b93d5;\">";
$mails = $mails . $generation;
$mails = $mails . "</font></h3></td><td style=\"width: 20%; text-align: right;\"><h3>Username : <font style=\"color: #0b93d5;\">";
$mails = $mails . $username;
$mails = $mails . "</font></h3></td><td style=\"width: 30%; text-align: right;\"><h3>School : <font style=\"color: #0b93d5;\">";
$mails = $mails . $school;
$mails = $mails . "</font></h3></td></tr></table><table style='border: 1px solid black; border-collapse: collapse;' width=\"100%\"> <tr> <td style=\"border: 1px solid black; border-collapse: collapse;width: 15%; background-color: grey\"><center><h3 style=\"color: white;margin: 5px 0px;\">Your Total <br>DQ Score</h3><br><h1 style=\"color: orange\">$nilaiself</h1></center></td><td rowspan=\"3\" style=\"border: 1px solid black; border-collapse: collapse;width: 70%;\">$image </td><td rowspan=\"3\" style=\"border: 1px solid black; border-collapse: collapse; width: 15%;\">$image2</td></tr><tr> <td><center><h3 style=\"color: darkgray;margin: 5px 0px;\">School<br>Average<br>DQ Score</h3><h1 style=\"color: grey\">$nilaischool</h1></center></td></tr><tr> <td><center><h3 style=\"color: darkgray;margin: 5px 0px;\">Class Average<br>DQ Score</h3><h1 style=\"color: grey\">$nilaiclass</h1></center></td></tr></table><br><br>";
$mails = $mails . "<table width='100%'> <tr> <td style='width: 5%; background-color: grey; border: 1px solid white; border-collapse: collapse;' rowspan='2'><br></td><td style='width:15%; background-color: grey; border: 1px solid white; border-collapse: collapse;' colspan='4'><br></td></tr><tr> <td style='width:15%; background-color: grey; color: White; border: 1px solid white; border-collapse: collapse;'><center><h3 style='color: white'>Type</h3></center></td><td style='width:20%; background-color: grey; color: White; border: 1px solid white; border-collapse: collapse;'><center><h3 style='color: white'>Level</h3></center></td><td style='width:55%; background-color: grey; color: White; border: 1px solid white; border-collapse: collapse;'><center><h3 style='color: white'>Meaning</h3></center></td><td style='width: 5%; background-color: grey; border: 1px solid white; border-collapse: collapse;'></td></tr></table><br><table width='100%'>";
$sql = "SELECT TIPE, SUM(VALUE) AS TOTAL FROM RESULT AS A LEFT JOIN QUESTION AS B ON A.QUESTION = B.ID LEFT JOIN CATEGORY AS C ON B.TIPE = C.KATEGORI WHERE A.USERID = $ID GROUP BY B.TIPE ORDER BY C.ID";
            $result = $conn->query($sql);
            $nilai = '';
            $totnil = 0;
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
                        if($x == 2 || $x == 4 || $x == 6)
                        {
                            $mails = $mails . "</table><br><br><br><br><table width='100%'>";
                        }
                    }
                    $isx = $x + 1;
                    $mails = $mails . "<tr>";
                    if($x == 0 || $x == 2 || $x == 4 || $x == 6)
                        {
                            $mails = $mails . "<td style='width:5%; background-color: grey;border: 1px solid grey; border-collapse: collapse;' rowspan='2'><br></td>";
                        }
                    $mails = $mails . "<td style='width:15%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> <img style='width: 140px;' src='http://dq-smartplus.com/res/type$isx.JPG'></td>";
                    if($x == 0)
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>".$arrLang[$lang]['excellent']."</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'>".$arrLang[$lang]['screen_time_a']."</td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>".$arrLang[$lang]['satisfactory']."</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'>".$arrLang[$lang]['screen_time_b']."</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>".$arrLang[$lang]['less_than_satisfactory']."</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'>".$arrLang[$lang]['screen_time_c']."</td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>".$arrLang[$lang]['requires_attention']."</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'>".$arrLang[$lang]['screen_time_d']."</td>";
                        }
                    }
                    elseif($x == 1)
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>Excellent</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Privacy mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungiprivacy anda dan orang lain. Anda dengan skor Management Privacy yang tinggi akan :<br>- Memiliki pengetahuan dan ketrampilan untuk menangani informasi pribadi yangdibagikan secara online dengan bijaksana.<br>- Memastikan dan melindungi privacy diri sendiri dan kontak lainnya .<br>- Tetap sadar bahwa privacy adalah hak asasi manusia yang mendasar.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telahmendukung pemahaman Privacy Management dengan baik. </td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Privacy mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungiprivacy anda dan orang lain. Anda dengan skor Management Privacy yang tinggi akan :<br>- Memiliki pengetahuan dan ketrampilan untuk menangani informasi pribadi yangdibagikan secara online dengan bijaksana.<br>- Memastikan dan melindungi privacy diri sendiri dan kontak lainnya.<br>- Tetap sadar bahwa privacy adalah hak asasi manusia yang mendasar.<br><br>Anda telah memahami dengan baik ! Prestasi anda dalam kategori PrivasiManagement memuaskan. </td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>Less than Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Privacy mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungiprivacy anda dan orang lain. Anda dengan skor Management Privacy yang tinggi akan :<br>- Memiliki pengetahuan dan ketrampilan untuk menangani informasi pribadi yangdibagikan secara online dengan bijaksana.<br>- Memastikan dan melindungi privacy diri sendiri dan kontak lainnya.<br>- Tetap sadar bahwa privacy adalah hak asasi manusia yang mendasar.<br><br>Prestasi anda dalam Privacy Management ini kurang memuaskan dan masihharus dikembangkan.</td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>Requires Attention</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Privacy mengukur kemampuan anda untuk menangani dengan bijaksana semua informasi pribadi yang dibagikan secara online untuk melindungiprivacy anda dan orang lain. Anda dengan skor Management Privacy yang tinggi akan :<br>- Memiliki pengetahuan dan ketrampilan untuk menangani informasi pribadi yangdibagikan secara online dengan bijaksana.<br>- Memastikan dan melindungi privacy diri sendiri dan kontak lainnya.<br>- Tetap sadar bahwa privacy adalah hak asasi manusia yang mendasar.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital. </td>";
                        }
                    }
                    elseif($x == 2)
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>Excellent</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menangani dengan bijak. Anda dengan skor Cyberbullying yang tinggi akan :<br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab<br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang.<br>- Tahu bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum menjadi tidak terkendali.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Cyberbullying Management dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menangani dengan bijak. Anda dengan skor Cyberbullying yang tinggi akan :<br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab<br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang.<br>- Tahu bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum menjadi tidak terkendali.<br><br>Anda telah memahami dengan baik Cyberbullying Management ! Prestasi anda dalam Kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>Less than Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menangani dengan bijak. Anda dengan skor Cyberbullying yang tinggi akan :<br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab<br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang.<br>- Tahu bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum menjadi tidak terkendali.<br><br>Prestasi anda dalam Cyberbullying Management kurang memuaskan dan masih harus dikembangkan. </td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>Requires Attention</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management Cyberbullying mengukur kemampuan anda untuk mendeteksi situasi cyberbullying dan menangani dengan bijak. Anda dengan skor Cyberbullying yang tinggi akan :<br>- Memiliki disiplin pribadi untuk menggunakan media digital secara aman danbertanggung jawab<br>- Mengetahui cara mendeteksi situasi ancaman cyber dan cara menangani situasi dengan tenang.<br>- Tahu bagaimana menangani masalah dengan bijaksana dan mencari bantuan dengan aman sebelum menjadi tidak terkendali.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($x == 3)
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>Excellent</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Citizen Identity mengukur kemampuan anda untuk membangun dan mengelola identities yang sehat dan berintegritas secara online dan offline. Anda dengan skor Digital Citizen Identity yang tinggi akan :<br>- Memahami sifat dunia digital dan menggunakan teknologi dan dunia digitaldengan percaya diri yang kuat.<br>- Memiliki pengetahuan dan ketrampilan untuk membangun dan mengelolaidentitas yang sehat.<br>- Menyadari menjadi warga negara global diruang digital.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telahmendukung pemahaman yang kuat tentang Digital Citizen Identity Skill dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Citizen Identity mengukur kemampuan anda untuk membangun dan mengelola identities yang sehat dan berintegritas secara online dan offline. Anda dengan skor Digital Citizen Identity yang tinggi akan :<br>- Memahami sifat dunia digital dan menggunakan teknologi dan dunia digitaldengan percaya diri yang kuat.<br>- Memiliki pengetahuan dan ketrampilan untuk membangun dan mengelolaidentitas yang sehat.<br>- Menyadari menjadi warga negara global diruang digital.<br><br>Anda telah memahami dengan baik Digital Citizen Identity Skill ! Prestasi andadalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>Less than Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Citizen Identity mengukur kemampuan anda untuk membangun dan mengelola identities yang sehat dan berintegritas secara online dan offline. Anda dengan skor Digital Citizen Identity yang tinggi akan :<br>- Memahami sifat dunia digital dan menggunakan teknologi dan dunia digitaldengan percaya diri yang kuat.<br>- Memiliki pengetahuan dan ketrampilan untuk membangun dan mengelolaidentitas yang sehat.<br>- Menyadari menjadi warga negara global diruang digital.<br><br>Prestasi anda dalam Digital Citizen Identity Skill ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>Requires Attention</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Citizen Identity mengukur kemampuan anda untuk membangun dan mengelola identities yang sehat dan berintegritas secara online dan offline. Anda dengan skor Digital Citizen Identity yang tinggi akan :<br>- Memahami sifat dunia digital dan menggunakan teknologi dan dunia digitaldengan percaya diri yang kuat.<br>- Memiliki pengetahuan dan ketrampilan untuk membangun dan mengelolaidentitas yang sehat.<br>- Menyadari menjadi warga negara global diruang digital.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($x == 4)
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>Excellent</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management jejak digital mengukur kemampuan anda untuk memahami sifat jejak digital dan konsekwensi kehidupan nyata dan untuk mengelola secara bertanggung jawab Anda dengan skor Management Jejak Digital yang tinggi akan :<br>- Memahami sifat Komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online meninggalkan jejak yang disebut jejakdigital.<br>- Waspadai sifat presisten dari jejak digital dan konsekwensi dalam kehidupannyata termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki ketrampilan untuk mengelola jejak digitalsecara bertanggung jawab.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Digital Footprint Management dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management jejak digital mengukur kemampuan anda untuk memahami sifat jejak digital dan konsekwensi kehidupan nyata dan untuk mengelola secara bertanggung jawab Anda dengan skor Management Jejak Digital yang tinggi akan :<br>- Memahami sifat Komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online meninggalkan jejak yang disebut jejakdigital.<br>- Waspadai sifat presisten dari jejak digital dan konsekwensi dalam kehidupannyata termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki ketrampilan untuk mengelola jejak digitalsecara bertanggung jawab.<br><br>Anda telah memahami dengan baik Digital Footprint Management ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>Less than Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management jejak digital mengukur kemampuan anda untuk memahami sifat jejak digital dan konsekwensi kehidupan nyata dan untuk mengelola secara bertanggung jawab Anda dengan skor Management Jejak Digital yang tinggi akan :<br>- Memahami sifat Komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online meninggalkan jejak yang disebut jejakdigital.<br>- Waspadai sifat presisten dari jejak digital dan konsekwensi dalam kehidupannyata termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki ketrampilan untuk mengelola jejak digitalsecara bertanggung jawab.<br><br>Prestasi anda dalam kategori Digital Footprint Management ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>Requires Attention</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management jejak digital mengukur kemampuan anda untuk memahami sifat jejak digital dan konsekwensi kehidupan nyata dan untuk mengelola secara bertanggung jawab Anda dengan skor Management Jejak Digital yang tinggi akan :<br>- Memahami sifat Komunikasi online dan mengetahui bahwa semua yang dikatakan dan dilakukan secara online meninggalkan jejak yang disebut jejakdigital.<br>- Waspadai sifat presisten dari jejak digital dan konsekwensi dalam kehidupannyata termasuk menciptakan dampak yang tidak diinginkan pada reputasi online anda. Sebaiknya anda memiliki ketrampilan untuk mengelola jejak digitalsecara bertanggung jawab.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($x == 5)
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>Excellent</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management keamanan cyber mengukur kemampuan anda untuk melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk Mengelola berbagaiserangan dunia Maya , yaitu SPAM / SCAM /Phishing. Anda dengan skor management keamanan cyber yang tinggi akan : <br>- Dapat mengenali dan melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti Spam/Scam dan Pishing<br>- Memiliki ketrampilan praktis seperti membuat sandi yang kuat dan tahu cara melindungi.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Cyber Security Management dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management keamanan cyber mengukur kemampuan anda untuk melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk Mengelola berbagaiserangan dunia Maya , yaitu SPAM / SCAM /Phishing. Anda dengan skor management keamanan cyber yang tinggi akan : <br>- Dapat mengenali dan melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti Spam/Scam dan Pishing<br>- Memiliki ketrampilan praktis seperti membuat sandi yang kuat dan tahu cara melindungi.<br><br>Anda telah memahami dengan baik Cyber Security Management ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>Less than Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management keamanan cyber mengukur kemampuan anda untuk melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk Mengelola berbagaiserangan dunia Maya , yaitu SPAM / SCAM /Phishing. Anda dengan skor management keamanan cyber yang tinggi akan : <br>- Dapat mengenali dan melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti Spam/Scam dan Pishing<br>- Memiliki ketrampilan praktis seperti membuat sandi yang kuat dan tahu cara melindungi.<br><br>Prestasi anda dalam kategori Cyber Security Management ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>Requires Attention</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Management keamanan cyber mengukur kemampuan anda untuk melindungi data pribadi dengan membuat kata Sandi yang kuat dan untuk Mengelola berbagaiserangan dunia Maya , yaitu SPAM / SCAM /Phishing. Anda dengan skor management keamanan cyber yang tinggi akan : <br>- Dapat mengenali dan melindungi diri sendiri dan orang lain dari berbagai serangan dunia maya seperti Spam/Scam dan Pishing<br>- Memiliki ketrampilan praktis seperti membuat sandi yang kuat dan tahu cara melindungi.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    elseif($x == 6)
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>Excellent</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Berpikir kritis mengukur kemampuan anda untuk membedakan antara informasiyang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dapat dipercaya dan dipertanyakan. Anda dengan skor berpikir kritis tinggi akan<br>- Memiliki pengetahuan dan ketrampilan untuk mengevaluasi informasi, konten dan kontak di internet dengan bijaksana.<br>- Memahami efek berbahaya dari informasi palsu, kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online.<br>- Menunjukkan pemikiran kritis saat membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dipercaya dan dipertanyakan.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Critical Thinking Skill dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Berpikir kritis mengukur kemampuan anda untuk membedakan antara informasiyang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dapat dipercaya dan dipertanyakan. Anda dengan skor berpikir kritis tinggi akan<br>- Memiliki pengetahuan dan ketrampilan untuk mengevaluasi informasi, konten dan kontak di internet dengan bijaksana.<br>- Memahami efek berbahaya dari informasi palsu, kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online.<br>- Menunjukkan pemikiran kritis saat membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dipercaya dan dipertanyakan.<br><br>Anda telah memahami dengan baik Critical Tihinking Skill ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>Less than Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Berpikir kritis mengukur kemampuan anda untuk membedakan antara informasiyang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dapat dipercaya dan dipertanyakan. Anda dengan skor berpikir kritis tinggi akan<br>- Memiliki pengetahuan dan ketrampilan untuk mengevaluasi informasi, konten dan kontak di internet dengan bijaksana.<br>- Memahami efek berbahaya dari informasi palsu, kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online.<br>- Menunjukkan pemikiran kritis saat membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dipercaya dan dipertanyakan.<br><br>Prestasi anda dalam kategori Critical Thinking Skill ini kurang memuaskan dan masih harus dikembangkan.</td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>Requires Attention</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Berpikir kritis mengukur kemampuan anda untuk membedakan antara informasiyang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dapat dipercaya dan dipertanyakan. Anda dengan skor berpikir kritis tinggi akan<br>- Memiliki pengetahuan dan ketrampilan untuk mengevaluasi informasi, konten dan kontak di internet dengan bijaksana.<br>- Memahami efek berbahaya dari informasi palsu, kekerasan, konten yang tidak pantas, serta resiko yang terkait dengan teman online.<br>- Menunjukkan pemikiran kritis saat membedakan antara informasi yang benar dan salah, konten yang baik dan berbahaya dan kontak online yang dipercaya dan dipertanyakan.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    else
                    {
                        if($row['TOTAL'] > 115)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Blue;border: 1px solid grey; border-collapse: collapse;'><center><h2>Excellent</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Empathy mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri dan orang lain secara online. Anda dengan skor Digital Empathy yang tinggi akan :<br>- Peka terhadap kebutuhan dan perasaan diri sendiri dan orang lain saat online bahkan tanpa interaksi tatap muka.<br>- Membangun hubungan baik dengan orang tua, guru dan teman baik secara online maupun offline.<br>- Tolak pola pikir menghakimi secara online, dan angkat bicara bagi mereka yang membutuhkan bantuan.<br><br>Selamat! performa anda dalam kategori ini sangat baik, terima kasih telah mendukung pemahaman yang kuat tentang Digital Empathy Skill dengan baik.</td>";
                        }
                        elseif($row['TOTAL'] >= 110)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orange;border: 1px solid grey; border-collapse: collapse;'><center><h2>Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Empathy mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri dan orang lain secara online. Anda dengan skor Digital Empathy yang tinggi akan :<br>- Peka terhadap kebutuhan dan perasaan diri sendiri dan orang lain saat online bahkan tanpa interaksi tatap muka.<br>- Membangun hubungan baik dengan orang tua, guru dan teman baik secara online maupun offline.<br>- Tolak pola pikir menghakimi secara online, dan angkat bicara bagi mereka yang membutuhkan bantuan.<br><br>Anda telah memahami dengan baik Digital Empathy Skill ! Prestasi anda dalam kategori ini memuaskan.</td>";
                        }
                        elseif($row['TOTAL'] >= 85)
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Orangered;border: 1px solid grey; border-collapse: collapse;'><center><h2>Less than Satisfactory</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Empathy mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri dan orang lain secara online. Anda dengan skor Digital Empathy yang tinggi akan :<br>- Peka terhadap kebutuhan dan perasaan diri sendiri dan orang lain saat online bahkan tanpa interaksi tatap muka.<br>- Membangun hubungan baik dengan orang tua, guru dan teman baik secara online maupun offline.<br>- Tolak pola pikir menghakimi secara online, dan angkat bicara bagi mereka yang membutuhkan bantuan.<br><br>Prestasi anda dalam kategori Digital Empathy Skill ini kurang memuaskan dan masih harus dikembangkan. </td>";
                        }
                        else
                        {
                            $mails = $mails . "<td style='width:20%; background-color: Red;border: 1px solid grey; border-collapse: collapse;'><center><h2>Requires Attention</h2></center></td>";
                            $mails = $mails . "<td style='width:55%; background-color: White;border: 1px solid grey; border-collapse: collapse;'> Digital Empathy mengukur kemampuan anda untuk berempati terhadap kebutuhan dan perasaan diri sendiri dan orang lain secara online. Anda dengan skor Digital Empathy yang tinggi akan :<br>- Peka terhadap kebutuhan dan perasaan diri sendiri dan orang lain saat online bahkan tanpa interaksi tatap muka.<br>- Membangun hubungan baik dengan orang tua, guru dan teman baik secara online maupun offline.<br>- Tolak pola pikir menghakimi secara online, dan angkat bicara bagi mereka yang membutuhkan bantuan.<br><br>Anda belum siap melakukan pengendalian diri saat menggunakan media digital.</td>";
                        }
                    }
                    $mails = $mails . "<td style='width: 5%; background-color: grey;'><br></td>";
                    $mails = $mails . "</tr>";
                    $x++;
                }
            } else {
                echo "0 results";
            }
$mails = $mails . "<tr> <td style='width: 5%; background-color: grey; border: 1px solid white; border-collapse: collapse;'><br></td><td style='width:15%; background-color: grey; border: 1px solid white; border-collapse: collapse;' colspan='4'><br></td></tr></table>";
$mails = $mails . "<center><img src='http://dq-smartplus.com/img/result2.jpeg' style='width: 750px; height:750px;'></center><script src=\"assets/js/vendor.min.js\"></script> <script src=\"assets/js/app.min.js\"></script> <script src=\"https://cdn.jsdelivr.net/npm/chart.js@2.8.0\"></script> <script src=\"https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.min.js\"></script> <script>Chart.helpers.merge(Chart.defaults.global.plugins.datalabels,{opacity: 1, textAlign: 'left', color: 'white', borderColor: '#11469e', borderWidth: 2, borderRadius: 100, font:{weight: 'bold', size: 12, lineHeight: 1 /* align v center */}, padding:{top: 5}, /* hover styling */ backgroundColor: function(context){return context.hovered ? context.dataset.borderColor : 'white';}, color: function(context){return context.hovered ? 'white' : context.dataset.borderColor;}, listeners:{enter: function(context){context.hovered=true; return true;}, leave: function(context){context.hovered=false; return true;}}});";

$sql = "SELECT * FROM `CONFIG`";
$result = $conn->query($sql);
$national = 0;
$global = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        if ($row['CONFIG'] == 'National Average')
        {
            $national = $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'];
        }
        elseif ($row['CONFIG'] == 'Global Average')
        {
            $global = $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'] . ', ' . $row['VALUE'];
        }
    }
} else {
    $mails = $mails . "0 results";
}
$mails = $mails . "var data={labels: [\"Privacy Management\", \"Critical Thinking\", \"Digital Footprint Management\", \"Digital Empathy\", \"Cyber Security Management\", \"Cyberbullying Management\", \"Screen Time Management\", \"Digital Citizen Identity\"], datasets: [{label: \"You\", backgroundColor: \"rgba(0, 200, 90, 0.2)\", borderColor: \"rgba(0, 141, 90, 0.2)\", data: [";
$mails = $mails . $nilai;
$mails = $mails . "]},{label: \"National Average\", backgroundColor: \"rgba(0, 141, 237, 0.2)\", borderColor: \"rgba(0, 63, 231, 0.2)\", borderDash: [10,5], data: [";
$mails = $mails . $national;
$mails = $mails . "]},{label: \"Global Average\", backgroundColor: \"rgba(242, 132, 231, 0.2)\", borderColor: \"rgba(183, 132, 216, 0.2)\", borderDash: [10,5], data: [";
$mails = $mails . $global;
$mails = $mails . "]}]}; var options={responsive: true, tooltips: false, title:{text: '', display: true, position: `bottom`,}, plugins:{datalabels:{/* formatter */ formatter: function(value, context){return context.chart.data.labels[context.value];}}}, scale:{angleLines:{display: true}, pointLabels:{fontSize: 15, fontColor: 'black', fontStyle: 'bold', callback: function(value, index, values){return value;}}, ticks:{suggestedMin: 0, suggestedMax: 100, stepSize: 25, maxTicksLimit: 11, display: false,}}, legend:{labels:{padding: 10, fontSize: 14, lineHeight: 30,},},}; var myChart=new Chart(document.getElementById(\"chart\"),{type: 'radar', data: data, options: options}); </script></body></html>";

echo "$mails";

require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();
$dompdf->loadHtml($mails);
$dompdf->render();
$output = $dompdf->output();
$filename = 'pdfresult/' . $username . '-' . $school . '.pdf';
file_put_contents('pdfresult/' . $username . '-' . $school . '.pdf', $output);

include "program/class.phpmailer.php";
$body = $body . "Dear Mr./Mrs. <b>" . $fullname . ",</b> </br></br>";
$body = $body . "With this email we inform your result from personality test in our site <b>www.dq-smartplus.com</b><br><br>";
$body = $body . "Thanks & Regards, <br>Dq Smartplus admin";

$mail = new PHPMailer;
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->IsHTML(true);
$mail->Host = "smtp.gmail.com"; //host masing2 provider email
$mail->SMTPDebug = false;
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = "mail.dq.smartplus@gmail.com"; //user email
$mail->Password = "wjqztiopibxpuegz"; //password email
$mail->SetFrom("mail.dq.smartplus@gmail.com","Admin DQ-Smartplus"); //set email pengirim
$mail->Subject = "Report of DQ-Smartplus Test"; //subyek email
$mail->addAttachment($filename, 'result.pdf');
$mail->AddAddress('yutublukman@gmail.com','yutublukman@gmail.com');  //tujuan email
//$mail->AddAddress('aryaubd@gmail.com','aryaubd@gmail.com');  //tujuan email
$mail->MsgHTML($body);
$mail->Send();
header("location: result.php");
exit();
?>