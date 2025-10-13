<?php
require 'program/koneksi.php';
require 'lang.php';
$lang = isset($_SESSION['lang'])?$_SESSION['lang']:"id";

$sql = "SELECT * FROM `account` WHERE ID = " . $_SESSION['ID'];
$result = $conn->query($sql);
$ID = $_SESSION['ID'];
$usernameSession = '';
$school = '';
$fullname = '';
$email = '';
$sendmail = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $usernameSession = $row['USERNAME'];
        $school = $row['SCHOOL'];
        $sendmail = $row['SENDMAIL'];
        $email = $row['EMAIL'];
        $fullname = $row['FULLNAME'];
    }
}

$sql = "SELECT DAY(MAX(ACTIVITY_ON)) AS TANGGAL, MONTHNAME(MAX(ACTIVITY_ON)) AS BULAN, YEAR(MAX(ACTIVITY_ON)) AS TAHUN FROM `RESULT` WHERE USERID = " . $_SESSION['ID'];
$result = $conn->query($sql);
$generation = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $generation = $row['TANGGAL'] . ' ' . $row['BULAN'] . ' ' . $row['TAHUN'];
    }
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


$national = '';
//$sql = "SELECT Q.TIPE, CONVERT((SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH'), INT) AS TOTAL FROM `RESULT` AS R LEFT JOIN `account` AS A ON R.USERID = A.ID LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH' GROUP BY Q.TIPE ";
$sql = "
    SELECT 
        Q.TIPE, 
        (SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH') AS TOTAL 
    FROM 
        `RESULT` AS R 
        LEFT JOIN `account` AS A ON R.USERID = A.ID 
        LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND STATE = 'FINISH' 
    GROUP 
        BY Q.TIPE 
";
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
$sql = "
    SELECT 
        Q.TIPE, 
        (SUM(R.VALUE) / (SELECT COUNT(ID) FROM QUESTION WHERE TIPE = Q.TIPE)) / (SELECT COUNT(ID) FROM account X WHERE X.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND X.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH') AS TOTAL 
    FROM 
        `RESULT` AS R 
        LEFT JOIN `account` AS A ON R.USERID = A.ID 
        LEFT JOIN `QUESTION` AS Q ON R.QUESTION = Q.ID WHERE A.SCHOOL = (SELECT SCHOOL FROM account WHERE ID = $ID) AND A.GRADE = (SELECT GRADE FROM account WHERE ID = $ID) AND STATE = 'FINISH' 
    GROUP 
    BY Q.TIPE
";
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


$sql = "SELECT TIPE, SUM(VALUE) AS TOTAL FROM RESULT AS A LEFT JOIN QUESTION AS B ON A.QUESTION = B.ID LEFT JOIN CATEGORY AS C ON B.TIPE = C.KATEGORI WHERE A.USERID = $ID GROUP BY B.TIPE ORDER BY C.ID";

$tempResult = [];
$resultData = $conn->query($sql);
$nilai = '';
$totnil = 0;
$tabul = '';
if ($resultData->num_rows > 0) {
    // output data of each row
    $x = 0;
    while($row = $resultData->fetch_assoc()) {
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

        $tempResult[] = $row;
        $x++;
    }
}

$totnil = $totnil / 8;
$nilaischool = $nilaischool / 8;
$nilaiclass = $nilaiclass / 8;

//================================================//

require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 6, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages() . " - http://dq-smartplus.com", 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(true);
$pdf->SetMargins(3, 5, 3, true);
$pdf->AddPage('L', 'A4');

$pdf->SetFont('helvetica', '', 12);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Image('img/topresult.PNG', 0, 0, 297);
$pdf->SetXY(3, $y + 40);

$border = 0;

$pdf->SetFont('Helvetica','', 12);
$pdf->SetTextColor(85, 85, 85);
$pdf->Cell(97, 8, $lang=="en"?"Date of Score Card Generation":"Tanggal Pembuatan Kartu Skor", $border, 0, "C");
$pdf->Cell(97, 8, $lang=="en"?"Username":"Nama Pengguna", $border, 0, "C");
$pdf->Cell(97, 8, $lang=="en"?"School":"Nama Sekolah", $border, 1, "C");

$pdf->SetFont('Helvetica','B', 12);
$pdf->SetTextColor(11, 147, 213);
$pdf->Cell(97, 8, $generation, $border, 0, "C");
$pdf->Cell(97, 8, $usernameSession, $border, 0, "C");
$pdf->Cell(97, 8, $school, $border, 1, "C");

$pdf->Cell(0, 8, "", $border, 1, "C");

$border = 1;

$tempX = $pdf->GetX();
$tempY = $pdf->GetY();

$pdf->Cell(60, 10, "", 'LT', 1, "C");
$pdf->SetFont('Helvetica','', 12);
$pdf->SetTextColor(85, 85, 85);
$pdf->Cell(60, 8, "Your Total DQ Score", 'L', 1, "C");
$pdf->SetFont('Helvetica','B', 16);
$pdf->SetTextColor(255, 165, 0);
$pdf->Cell(60, 8, intval($totnil), 'L', 1, "C");
$pdf->Cell(60, 10, "", 'L', 1, "C");

$pdf->Cell(60, 10, "", 'LT', 1, "C");
$pdf->SetFont('Helvetica','', 12);
$pdf->SetTextColor(85, 85, 85);
$pdf->Cell(60, 8, "School Average DQ Score", 'L', 1, "C");
$pdf->SetFont('Helvetica','B', 16);
$pdf->SetTextColor(255, 165, 0);
$pdf->Cell(60, 8, intval($nilaischool), 'L', 1, "C");
$pdf->Cell(60, 10, "", 'L', 1, "C");

$pdf->Cell(60, 10, "", 'LT', 1, "C");
$pdf->SetFont('Helvetica','', 12);
$pdf->SetTextColor(85, 85, 85);
$pdf->Cell(60, 8, "Class Average DQ Score", 'L', 1, "C");
$pdf->SetFont('Helvetica','B', 16);
$pdf->SetTextColor(255, 165, 0);
$pdf->Cell(60, 8, intval($nilaiclass), 'L', 1, "C");
$pdf->Cell(60, 10, "", 'LB', 1, "C");

$pdf->SetFont('Helvetica','', 12);
$pdf->SetTextColor(85, 85, 85);
$imgChart = 'chart/' . $_SESSION['ID'] . 's.jpg';
$html = '<br/><br/><br/><img src="'.$imgChart.'" />';
$tempX = $tempX + 60;
$pdf->writeHTMLCell(171, (9 * 12), $tempX, $tempY, $html, 1, 0, false, true, 'C', true);

$tempX = $tempX + 171;
$html = '<br/><br/><img height="250" src="img/isitabelkanan.PNG" />';
$pdf->writeHTMLCell(60, (9 * 12), $tempX, $tempY, $html, 1, 0, false, true, 'C', true);

// $pdf->SetFont('Helvetica','', 12);
// $pdf->SetTextColor(85, 85, 85);
// $pdf->Cell(68, 8, "Your Total DQ Score", $border, 0, "C");
// $pdf->Cell(68, 8, "School Average DQ Score", $border, 0, "C");
// $pdf->Cell(68, 8, "Class Average DQ Score", $border, 1, "C");

// $pdf->SetFont('Helvetica','B', 14);
// $pdf->SetTextColor(255, 165, 0);
// $pdf->Cell(68, 8, intval($totnil), $border, 0, "C");
// $pdf->Cell(68, 8, intval($nilaischool), $border, 0, "C");
// $pdf->Cell(68, 8, intval($nilaiclass), $border, 1, "C");

// $pdf->Cell(0, 24, "", $border, 1, "C");

// $x = $pdf->GetX();
// $y = $pdf->GetY();
// $address = 'chart/' . $_SESSION['ID'] . 's.jpg';
// $pdf->Cell(2, 8, "", $border, 0, "C");
// $pdf->Image($address, 2, $y, 200, 100);
// $pdf->Cell(2, 8, "", $border, 0, "C");

$pdf->AddPage('L', 'A4');
//$pdf->SetXY($x + 0, $y + 120);

$pdf->SetFont('Helvetica','B', 14);
$pdf->SetTextColor(68, 68, 68);
$pdf->Cell(60, 8, 'Type', 1, 0, 'C', 0, '', 0);
$pdf->Cell(60, 8, 'Level', 1, 0, 'C', 0, '', 0);
$pdf->Cell(171, 8, 'Meaning', 1, 1, 'C', 0, '', 0);

$arrImage = array(
    'Screen Time' => 'res/type1.JPG',
    'Privacy Management' => 'res/type2.JPG',
    'Cyberbullying' => 'res/type3.JPG',
    'Digital Citizen Identity' => 'res/type4.JPG',
    'Digital Footprint' => 'res/type5.JPG',
    'Cyber Security Management' => 'res/type6.JPG',
    'Critical Thinking' => 'res/type7.JPG',
    'Digital Empathy' => 'res/type8.JPG'
);

$x = $pdf->GetX();
$y = $pdf->GetY();
$i = 1;
foreach($tempResult as $row){
    $tipe = $row['TIPE'];
    $total = $row['TOTAL'];
    $text = $arrLang[$lang]['digital_empathy_a'];
    $image = $arrImage[$tipe];
    $level = $lang;
    
    if($tipe == 'Screen Time') {
        if($total > 115) {
            $text = $arrLang[$lang]['screen_time_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['screen_time_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['screen_time_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['screen_time_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }
    else if ($tipe == 'Privacy Management') {
        if($total > 115) {
            $text = $arrLang[$lang]['privacy_management_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['privacy_management_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['privacy_management_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['privacy_management_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }
    else if ($tipe == 'Cyberbullying') {
        if($total > 115) {
            $text = $arrLang[$lang]['cyberbullying_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['cyberbullying_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['cyberbullying_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['cyberbullying_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }
    else if ($tipe == 'Digital Citizen Identity') {
        if($total > 115) {
            $text = $arrLang[$lang]['digital_citizen_identity_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['digital_citizen_identity_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['digital_citizen_identity_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['digital_citizen_identity_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }
    else if ($tipe == 'Digital Footprint') {
        if($total > 115) {
            $text = $arrLang[$lang]['digital_footprint_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['digital_footprint_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['digital_footprint_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['digital_footprint_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }
    else if ($tipe == 'Cyber Security Management') {
        if($total > 115) {
            $text = $arrLang[$lang]['cyber_security_management_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['cyber_security_management_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['cyber_security_management_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['cyber_security_management_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }
    else if ($tipe == 'Critical Thinking') {
        if($total > 115) {
            $text = $arrLang[$lang]['critical_thinking_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['critical_thinking_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['critical_thinking_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['critical_thinking_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }
    else {
        if($total > 115) {
            $text = $arrLang[$lang]['digital_empathy_a'];
            $level = $arrLang[$lang]['excellent'];
        }
        else if($total >= 100) {
            $text = $arrLang[$lang]['digital_empathy_b'];
            $level = $arrLang[$lang]['satisfactory'];
        }
        else if($total >= 85) {
            $text = $arrLang[$lang]['digital_empathy_c'];
            $level = $arrLang[$lang]['less_than_satisfactory'];
        }
        else {
            $text = $arrLang[$lang]['digital_empathy_d'];
            $level = $arrLang[$lang]['requires_attention'];
        }
    }

    $meaning = str_replace("<br/>", "\n", $text);

    $h = $pdf->getStringHeight(171, $meaning, true, false, '', 0);
    $h = round($h + 1, 0);

    $pdf->Image($image, '', '', 60, $h, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

    $pdf->SetFont('Helvetica','B', 14);
    if ($level == $arrLang[$lang]['excellent']) {
        //$pdf->SetTextColor(38, 14, 131);
        $pdf->SetFillColor(38, 14, 131);
    } else if ($level == $arrLang[$lang]['satisfactory']) {
        //$pdf->SetTextColor(245, 138, 10);
        $pdf->SetFillColor(245, 138, 10);
    } else if ($level == $arrLang[$lang]['less_than_satisfactory']) {
        //$pdf->SetTextColor(237, 34, 7);
        $pdf->SetFillColor(237, 34, 7);
    } else if ($level == $arrLang[$lang]['requires_attention']) {
        //$pdf->SetTextColor(196, 0, 16);
        $pdf->SetFillColor(196, 0, 16);
    }
    
    $pdf->SetTextColor(255, 255, 255);
    $pdf->MultiCell(60, $h, $level, 1, 'C', 1, 0, '', '', true, 0, false, true, 0, 'M', true);
    
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(68, 68, 68);
    $pdf->SetFont('Helvetica','', 12);
    $pdf->MultiCell(171, $h, $meaning, 1, 'L', 0, 1, '', '', true, 0, false, true, 0, 'M');
}

$pdf->AddPage('L', 'A4');

$imgFooter = $lang=="en"?"img/result-en.webp":"img/result-id.webp";
// $pdf->Cell(2, 8, "", 0, 0, "C");
// $pdf->Image($address, 52, $y, 100, 92);
// $pdf->Cell(2, 8, "", 0, 0, "C");
$html = '<img height="400" src="'.$imgFooter.'" />';
$pdf->writeHTMLCell(291, 10, '', '', $html, 0, 1, false, true, 'C', true);

//$filename = "xxxx.pdf";
//$pdf->Output($filename, 'I');

$filename = 'filepdf/pdf-' . $usernameSession . '-' . $school . '.pdf';
$pathName = __DIR__  . '/' . $filename;
$output = $pdf->Output($pathName, 'F');

$fullname = $fullname == "" ? $usernameSession : $fullname;
include "program/class.phpmailer.php";
$body = $body . "Dear Mr./Mrs. <b>" . $fullname . ",</b> </br></br>";
$body = $body . "With this email we inform your result from Digital Quotient test in our site <b>www.dq-smartplus.com</b><br/><br/>";
$body = $body . "Thanks & Regards, <br/>Dq Smartplus admin";

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
$mail->AddAddress($email, $usernameSession);  //tujuan email
$mail->MsgHTML($body);
$mail->Send();
header("location: result.php");
exit();
?>
