<?php
include "program/class.phpmailer.php";
$mail = new PHPMailer;
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->IsHTML(true);
$mail->Host = "smtp.gmail.com"; //host masing2 provider email
$mail->SMTPDebug = 1;
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = 'mail.dq.smartplus@gmail.com'; //user email
$mail->Password = "wjqztiopibxpuegz"; //password email
$mail->SetFrom('mail.dq.smartplus@gmail.com',"Admin DQ-Smartplus"); //set email pengirim
$mail->Subject = "Test send mail"; //subyek email
$mail->AddAddress("aryaubd@gmail.com", "Arya");  //tujuan email
$body = '<h1>Helo mail</h1>';
$mail->MsgHTML($body);

if ($mail->Send()) {
    echo "OK";
} else {
    echo "Gagal";
}
?>