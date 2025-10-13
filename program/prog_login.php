<?php
include 'koneksi.php';

$sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE email = '" . $_POST['email'] . "'";
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
    $sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE email = '" . $_POST['email'] . "' and password = '" . $_POST['password'] . "'";
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
        $sql = "SELECT * FROM `account` WHERE email = '" . $_POST['email'] . "' and password = '" . $_POST['password'] . "'";
        $result = $conn->query($sql);
        $username = '';
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                session_start();
                $_SESSION['ID'] = $row['ID'];
                if ($row['RANK'] == 'student')
                {
                    if ($row['STATE'] == 'ujian')
                    {
                        header("location: main.php'");
                        exit();
                    }
                    elseif ($row['STATE'] == 'upload')
                    {
                        header("location: upload.php'");
                        exit();
                    }
                    else
                    {
                        header("location: result.php'");
                        exit();
                    }
                }
                else
                {
                    header("location: admin.php'");
                    exit();
                }
            }
        }
    }
    else
    {
        header("location: signup.php?error='Password salah'");
        exit();
    }
}
else
{
    header("location: signup.php?error='Akun untuk email " . $_POST['email'] . " tidak terdaftar'");
    exit();
}