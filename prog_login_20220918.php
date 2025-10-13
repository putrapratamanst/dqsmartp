<?php
include 'program/koneksi.php';

$sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE USERNAME = '" . $_GET['email'] . "'";
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
    $sql = "SELECT COUNT(*) AS TOTAL FROM `account` WHERE USERNAME = '" . $_GET['email'] . "' and password = '" . $_GET['password'] . "'";
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
        $sql = "SELECT * FROM `account` WHERE USERNAME = '" . $_GET['email'] . "' and password = '" . $_GET['password'] . "' order by ID DESC";
        $result = $conn->query($sql);
        $username = '';
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                session_start();
                $_SESSION['ID'] = $row['ID'];
                $sql = "SELECT * FROM VERIVICATION WHERE USERNAME = '" . $_GET['email'] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($rowx = $result->fetch_assoc()) {
                        if($rowx['STAT'] == '0')
                        {
                            header("location: index.php?error=Your username was not verificated using link that we already sent to your email.");
                            exit();
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
        header("location: index.php?error=Password salah");
        exit();
    }
}
else
{
    header("location: index.php?error=Akun untuk username " . $_GET['email'] . " tidak terdaftar");
    exit();
}