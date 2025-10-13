<?php

include "program/class.phpmailer.php";
$body = $body . "Dear Mr./Ms. <b>" . $username . ",</b> </br></br>";
$body = $body . "With this email we inform you our QRIS to pay our services</b><br><br>";
$body = $body . "<img src='http://dq-smartplus.com/img/qris.jpg' style='width: 300px; height: 450px;'><br><br>";
$body = $body . "Thanks & Regards, <br>dq-smartplus";
            $mail = new PHPMailer;
            $mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->IsHTML(true);
$mail->Host = "smtp.gmail.com"; //host masing2 provider email
$mail->SMTPDebug = false;
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = "mail.dq.smartplus@gmail.com"; //user email
$mail->Password = "superman_22"; //password email
$mail->SetFrom("mail.dq.smartplus@gmail.com","Admin DQ-Smartplus"); //set email pengirim
$mail->Subject = "DQ-Smartplus Registration"; //subyek email
$mail->addAttachment('img/qris.jpg', 'qris.jpg');
$mail->AddEmbeddedImage('img/qris.jpg', 'logo_2u');
$mail->AddAddress('yutublukman@gmail.com','yutublukman@gmail.com');  //tujuan email
$mail->MsgHTML($body);
$mail->Send();

?>