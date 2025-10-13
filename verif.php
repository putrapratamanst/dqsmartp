<?php
include 'program/koneksi.php';

$sql = "UPDATE VERIVICATION SET STAT = 1 WHERE ID = '" . $_GET['id'] . "'";
if ($conn->query($sql) === TRUE) {
    $sql = "SELECT * FROM `account` WHERE USERNAME = (SELECT USERNAME FROM VERIVICATION WHERE ID = '" . $_GET['id'] . "')";
    $email = '';
    $username = '';
    $fullname = '';
    echo "<script>console.log('$sql');</script>";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $email = $row['EMAIL'];
            $username = $row['USERNAME'];
            $fullname = $row['FULLNAME'];
            
            if (is_null($row['VOUCHER']))
            {
                echo "<br><br>";
                echo "<center>";
                echo "<h1>Your account has been verified and Payment information has been sent to your email</h1>";
                echo "<a href='index.php'><h2>Back to login page</h2></a>";
                echo "</center>";
                $body = $body . "Dear Mr./Mrs. <b>" . $fullname . ",</b> With this email we inform you our QRIS to pay our services. Please complete your payment of IDR 150.000,-</br></br>";
                $body = $body . "<img width='150' src='dq-smartplus.com/img/qris.jpg'></br></br>";
                $body = $body . "Thanks & Regards, <br>Dq Smartplus admin";
                include "program/class.phpmailer.php";
                $mail = new PHPMailer;
                $mail->IsSMTP();
                $mail->SMTPSecure = 'ssl';
                $mail->IsHTML(true);
                $mail->Host = "smtp.gmail.com"; //host masing2 provider email
                $mail->SMTPDebug = false;
                $mail->Port = 465;
                $mail->SMTPAuth = true;
                $mail->Username = "mail.dq.smartplus@gmail.com"; //user email
                $mail->Password = "wjqztiopibxpuegz"; //"superman_22"; //password email
                $mail->SetFrom("mail.dq.smartplus@gmail.com","Admin DQ-Smartplus"); //set email pengirim
                $mail->Subject = "DQ-Smartplus Registration"; //subyek email
                $mail->AddAddress($email,$username);  //tujuan email
                $mail->MsgHTML($body);
                $mail->Send();
            }
            else
            {
                echo "<br><br>";
                echo "<center>";
                echo "<h1>Your account has been verified</h1>";
                echo "<a href='index.php'><h2>Back to login page</h2></a>";
                echo "</center>";
                echo "<script>console.log('Voucher : " . $row['VOUCHER'] . "');</script>";
            }
        }
    }
    
    
}
else
{
    echo "<br><br>";
    echo "<center>";
    echo "<h1>Verfication Failed ask admin for information.</h1>";
    echo "<a href='index.php'><h2>Back to login page</h2></a>";
    echo "</center>";
    echo "<script>console.log('$sql');</script>";
}
?>