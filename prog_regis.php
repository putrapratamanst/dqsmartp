<?php
include 'program/koneksi.php';
$lang = isset($_SESSION['lang'])?$_SESSION['lang']:"";
$lang = $lang==""?"id":$lang;
$inx = 1;
echo "state $inx";
$inx++;
$sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE username = '" . $_GET['username'] . "'";
$result = $conn->query($sql);
$hasil = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $hasil = $row['TOTAL'];
    }
} else {
    echo "0 results";
}
echo "state $inx";
$inx++;
if ($hasil > 0)
{
    echo "state $inx F";
$inx++;
    $sql = "SELECT * FROM `account` WHERE username = '" . $_GET['username'] . "'";
    $result = $conn->query($sql);
    $username = '';
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $username = $row['EMAIL'];
            $err = $conn->error;
            
            if($lang=="id") {
                header("location: signup.php?error=Nama pengguna ($username) sudah terdaftar");
            } else {
                header("location: signup.php?error=Username ($username) already exist");
            }
            exit();
        }
    } else {
        echo "0 results";
    }

    //header("location: signup.php?error=Username sudah ada dengan email $username");
    //exit();
}
else
{
    echo "state $inx X";
$inx++;
    if(isset($_GET['voucher']) && strlen(trim($_GET['voucher'])) != 0)
    {
        echo "state $inx";
        $inx++;
        $sql = "SELECT * FROM `VOUCHERS`  WHERE VOUCHER = '" . $_GET['voucher'] . "'";
        $result = $conn->query($sql);
        $username = '';
        if ($result->num_rows > 0) {
            // output data of each row
            echo "state $inx";
            $inx++;
            while($row = $result->fetch_assoc()) {
                if($row['STATE'] == 'N')
                {
                    echo "state $inx";
                    $inx++;
                    $username = $_GET['username'];
                    $password = $_GET['password'];
                    $email = $_GET['email'];
                    $voucher = $_GET['voucher'];
                    $school = $_GET['school'];
                    $gender = $_GET['gender'];
                    $fullname = $_GET['fullname'];
                    $phone = $_GET['phone'];
                    $grade = $_GET['grade'];
                    $sql = "INSERT INTO account (USERNAME, EMAIL, PASSWORD, SCHOOL, VOUCHER, RANK, STATE, FULLNAME, PHONE, GRADE, GENDER) VALUES ('$username', '$email', '$password', '$school', '$voucher', 'student', 'ujian', '$fullname', '$phone', '$grade', '$gender')";

                    if ($conn->query($sql) === TRUE) {
                        $sql = "UPDATE `VOUCHERS` SET ASSIGN_ON = '$username', STATE = 'Y', UPDATED_AT = NOW() WHERE VOUCHER = '$voucher'";
                        if ($conn->query($sql) === TRUE) {
                            $IDV = '';
                            $sql = "SELECT UUID() AS ID";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $IDV = $row['ID'];
                                }
                            }
                            $sql = "INSERT INTO VERIVICATION VALUES ('$IDV', '$username', 0)";
                            if ($conn->query($sql) === TRUE) {
                                
                                include "program/class.phpmailer.php";
                                $body = $body . "Dear Mr./Mrs. <b>" . $fullname . ",</b> </br></br>";
                                $body = $body . "With this email we inform you to verify your account here.<br>";
                                $body = $body . "<a href='http://dq-smartplus.com/verif.php?id=" . $IDV . "'><h2>CLICK FOR VERIFICATION</h2></a><br><br>";
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
                                $mail->Subject = "DQ-Smartplus Registration Email Verfication"; //subyek email
                                $mail->AddAddress($email,$username);  //tujuan email
                                $mail->MsgHTML($body);
                                $mail->Send();
                                
                                if($lang=="id") {
                                    header("location: index.php?sukses=Akun untuk username $username berhasil di buat. Check email anda di $email untuk memverifikasi akun anda.");
                                } else {
                                    header("location: index.php?sukses=The account with username " . $username . " has been created successfully. Please check your email to verify.");
                                }
                                exit();
                            }
                        }
                        else {
                            $err = $conn->error;
                            if($lang=="id") {
                                header("location: signup.php?error=Gagal mengupdate voucher. Err: ($err)");
                            } else {
                                header("location: signup.php?error=Update voucher data Error ($err)");
                            }
                            exit();
                        }
                    } else {
                        $err = $conn->error;
                        if($lang=="id") {
                            header("location: signup.php?error=Gagal menginput data. Err ($err)");
                        }
                        else {
                            header("location: signup.php?error=Insert data Error ($err)");   
                        }
                        exit();
                    }
                }
                else
                {
                    if($lang=="id") {
                        header("location: signup.php?error=Kode voucher telah di gunakan mohon cek kembali");
                    } else {
                        header("location: signup.php?error=The voucher code has been used, please check again");   
                    }
                    exit();
                }
            }
        } else {
            if($lang=="id") {
                header("location: signup.php?error=Kode voucher " . $username . " tidak dapat ditemukan");
            } else {
                header("location: signup.php?error=Voucher code " . $username . " not found");
            }
            
            exit();
        }
    }
    else
    {
        $IDV = '';
        $sql = "SELECT UUID() AS ID";
        $result = $conn->query($sql);
        $username = '';
        if ($result->num_rows > 0) {
            // output data of each row
            echo "state $inx";
            $inx++;
            while($row = $result->fetch_assoc()) {
                $IDV = $row['ID'];
            }
        }
        $username = $_GET['username'];
        $password = $_GET['password'];
        $email = $_GET['email'];
        $voucher = $_GET['voucher'];
        $school = $_GET['school'];
        $gender = $_GET['gender'];
        $fullname = $_GET['fullname'];
        $phone = $_GET['phone'];
        $grade = $_GET['grade'];
        $sql = "INSERT INTO account (USERNAME, EMAIL, PASSWORD, SCHOOL, RANK, STATE, FULLNAME, PHONE, GRADE, GENDER) VALUES ('$username', '$email', '$password', '$school', 'student', 'upload', '$fullname', '$phone', '$grade', '$gender')";

        if ($conn->query($sql) === TRUE) {
            $sql = "INSERT INTO VERIVICATION VALUES ('$IDV', '$username', 0)";

            if ($conn->query($sql) === TRUE) {
                include "program/class.phpmailer.php";
                $body = $body . "Dear Mr./Mrs. <b>" . $fullname . ",</b> </br></br>";
                $body = $body . "With this email we inform you to verify your account here.<br>";
                $body = $body . "<a href='http://dq-smartplus.com/verif.php?id=" . $IDV . "'><h2>CLICK FOR VERIFICATION</h2></a><br><br>";
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
                $mail->Subject = "DQ-Smartplus Registration Email Verfication"; //subyek email
                $mail->AddAddress($email,$username);  //tujuan email
                $mail->MsgHTML($body);
                $mail->Send();
                
                if($lang=="id") {
                    header("location: index.php?error=Akun dengan nama pengguna " . $username . " telah berhasil dibuat. Silakan periksa email Anda untuk memverifikasi.");
                } else {
                    header("location: index.php?error=The account with username " . $username . " has been created successfully. Please check your email to verify.");
                }
                exit();
            }
        } else {
            $err = $conn->error;
            if($lang=="id") {
                header("location: signup.php?error=Gagal menginput data. Err ($err)");
            }
            else {
                header("location: signup.php?error=Insert data Error ($err)");   
            }
            exit();
        }
    }
}
include 'program/close.php';
?>