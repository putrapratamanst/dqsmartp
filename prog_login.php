<?php
include 'program/koneksi.php';

$username = isset($_POST['email'])?$_POST['email']:"";
$password = isset($_POST['password'])?$_POST['password']:"";
$lang = isset($_SESSION['lang'])?$_SESSION['lang']:"";
if($lang == "") {
    $lang = "id";
}

$sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE USERNAME = '" . $username . "'";
$result = $conn->query($sql);
$hasil = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $hasil = $row['TOTAL'];
        echo "<script>console.log('Total : $hasil')";
    }
} else {
    echo "0 results";
}
echo "<script>console.log('Running clear for $sql')";
if ($hasil != 0)
{
    $sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE USERNAME = '" . $username . "' and password = '" . $password . "'";
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
    if ($hasil != 0)
    {
        $sql = "SELECT * FROM `account` WHERE USERNAME = '" . $username . "' and password = '" . $password . "' order by ID DESC";
        $result = $conn->query($sql);
        $username = '';
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                session_start();
                $_SESSION['ID'] = $row['ID'];
                $sql = "SELECT * FROM VERIVICATION WHERE USERNAME = '" . $username . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($rowx = $result->fetch_assoc()) {
                        if($rowx['STAT'] == '0')
                        {
                            if ($lang == "id") {
                                header("location: index.php?error=Akun dengan nama pengguna " . $username . " telah berhasil dibuat. Silakan periksa email Anda untuk memverifikasi.");
                                exit();
                            }
                            else {
                                header("location: index.php?error=The account with username " . $username . " has been created successfully. Please check your email to verify.");
                                exit();
                            }
                            
                        }
                        else
                        {
                            if ($row['RANK'] == 'student')
                            {
                                if ($row['STATE'] == 'ujian')
                                {
                                    header("location: start.php");
                                    exit();
                                }
                                elseif ($row['STATE'] == 'upload')
                                {
                                    header("location: upload.php");
                                    exit();
                                }
                                else
                                {
                                    header("location: result.php?");
                                    exit();
                                }
                            }
                            else
                            {
                                header("location: admin.php");
                                exit();
                            }
                        }
                    }
                }
                else
                {
                    if ($row['RANK'] == 'student')
                            {
                                if ($row['STATE'] == 'ujian')
                                {
                                    header("location: start.php");
                                    exit();
                                }
                                elseif ($row['STATE'] == 'upload')
                                {
                                    header("location: upload.php");
                                    exit();
                                }
                                else
                                {
                                    header("location: result.php");
                                    exit();
                                }
                            }
                            else
                            {
                                header("location: admin.php");
                                exit();
                            }
                }
            }
        }
    }
    else
    {
        if ($lang == "id") {
            header("location: index.php?error=Nama pengguna atau kata sandi salah");
        }
        else {
            header("location: index.php?error=Invalid username or password");
        }
        exit();
    }
}
else
{
    if ($lang == "id") {
        header("location: index.php?error=Akun dengan nama pengguna <b>" . $username . "</b> tidak terdaftar");
    } else {
        header("location: index.php?error=Account with username <b>" . $username . "</b> not registered");
    }
    exit();
}