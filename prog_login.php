<?php

include 'program/koneksi.php';


$username = isset($_POST['email']) ? $_POST['email'] : "";

$password = isset($_POST['password']) ? $_POST['password'] : "";

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : "";

if ($lang == "") {

    $lanng = "id";
}



$sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE USERNAME = '" . $username . "'";

$result = $conn->query($sql);

$hasil = 0;

if ($result->num_rows > 0) {

    // output data of each row

    while ($row = $result->fetch_assoc()) {

        $hasil = $row['TOTAL'];

        echo "<script>console.log('Total : $hasil')";
    }
} else {

    echo "0 results";
}

echo "<script>console.log('Running clear for $sql')";

if ($hasil != 0) {

    $sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE USERNAME = '" . $username . "' and password = '" . $password . "'";

    $result = $conn->query($sql);

    $hasil = 0;

    if ($result->num_rows > 0) {

        // output data of each row

        while ($row = $result->fetch_assoc()) {

            $hasil = $row['TOTAL'];
        }
    } else {

        echo "0 results";
    }

    if ($hasil != 0) {

        // Check if user has assigned voucher but hasn't entered it

        $user_email_check = $conn->query("SELECT EMAIL FROM account WHERE USERNAME = '$username'");

        $user_row = $user_email_check->fetch_assoc();

        $user_email = $user_row['EMAIL'];

        

        $voucher_check = $conn->query("SELECT COUNT(*) as cnt FROM VOUCHERS WHERE ASSIGN_ON = '$user_email' AND STATE = 'N'");

        $voucher_row = $voucher_check->fetch_assoc();

        

        if ($voucher_row['cnt'] > 0 && (!isset($_POST['voucher']) || empty(trim($_POST['voucher'])))) {

            if ($lang == "id") {

                header("location: index.php?error=Silakan lakukan pembayaran atau masukkan kode voucher untuk melanjutkan.&email=" . urlencode($username) . "&password=" . urlencode($password) . "&voucher=" . urlencode(isset($_POST['voucher']) ? $_POST['voucher'] : ''));

            } else {

                header("location: index.php?error=Please make payment or enter your voucher code to continue.&email=" . urlencode($username) . "&password=" . urlencode($password) . "&voucher=" . urlencode(isset($_POST['voucher']) ? $_POST['voucher'] : ''));

            }

            exit();

        }

        

        // If voucher is provided, validate it

        if (isset($_POST['voucher']) && !empty(trim($_POST['voucher']))) {

            $voucher_code = trim($_POST['voucher']);

            $voucher_sql = "SELECT * FROM VOUCHERS WHERE VOUCHER = '$voucher_code' AND ASSIGN_ON = '$user_email' AND STATE = 'N'";

            $voucher_result = $conn->query($voucher_sql);

            

            if ($voucher_result->num_rows == 0) {

                if ($lang == "id") {

                    header("location: index.php?error=Kode voucher tidak valid atau tidak sesuai dengan akun Anda.&email=" . urlencode($username) . "&password=" . urlencode($password) . "&voucher=" . urlencode($voucher_code));

                } else {

                    header("location: index.php?error=Invalid voucher code or not assigned to your account.&email=" . urlencode($username) . "&password=" . urlencode($password) . "&voucher=" . urlencode($voucher_code));

                }

                exit();

            }

        }

        $sql = "SELECT * FROM `account` WHERE USERNAME = '" . $username . "' and password = '" . $password . "' order by ID DESC";

        $result = $conn->query($sql);

        $username = '';

        if ($result->num_rows > 0) {

            // output data of each row

            while ($row = $result->fetch_assoc()) {

                // session_start();
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['ID'] = $row['ID'];
                

                // If voucher was provided and validated, update account and voucher

                if (isset($_POST['voucher']) && !empty(trim($_POST['voucher']))) {

                    $voucher_code = trim($_POST['voucher']);

                    $update_sql = "UPDATE account SET VOUCHER = '$voucher_code', STATE = 'ujian' WHERE ID = " . $row['ID'];

                    if ($conn->query($update_sql)) {

                        $conn->query("UPDATE VOUCHERS SET STATE = 'Y', UPDATED_AT = NOW() WHERE VOUCHER = '$voucher_code'");

                    }

                }
                $sql = "SELECT * FROM VERIVICATION WHERE USERNAME = '" . $username . "'";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {

                    while ($rowx = $result->fetch_assoc()) {

                        if ($rowx['STAT'] == '0') {

                            if ($lang == "id") {

                                header("location: index.php?error=Akun dengan nama pengguna " . $username . " telah berhasil dibuat. Silakan periksa email Anda untuk memverifikasi.");

                                exit();
                            } else {

                                header("location: index.php?error=The account with username " . $username . " has been created successfully. Please check your email to verify.");

                                exit();
                            }
                        } else {

                            if ($row['RANK'] == 'student') {

                                if ($row['STATE'] == 'ujian') {

                                    header("location: start.php");

                                    exit();
                                } elseif ($row['STATE'] == 'upload') {

                                    header("location: upload.php");

                                    exit();
                                } else {

                                    header("location: result.php?");

                                    exit();
                                }
                            } else {

                                header("location: admin.php");

                                exit();
                            }
                        }
                    }
                } else {

                    if ($row['RANK'] == 'student') {

                        if ($row['STATE'] == 'ujian') {

                            header("location: start.php");

                            exit();
                        } elseif ($row['STATE'] == 'upload') {

                            header("location: upload.php");

                            exit();
                        } else {

                            header("location: result.php");

                            exit();
                        }
                    } else {

                        header("location: admin.php");

                        exit();
                    }
                }
            }
        }
    } else {

        if ($lang == "id") {

            header("location: index.php?error=Nama pengguna atau kata sandi salah");
        } else {

            header("location: index.php?error=Invalid username or password");
        }

        exit();
    }
} else {

    if ($lang == "id") {

        header("location: index.php?error=Akun dengan nama pengguna <b>" . $username . "</b> tidak terdaftar");
    } else {

        header("location: index.php?error=Account with username <b>" . $username . "</b> not registered");
    }

    exit();
}
