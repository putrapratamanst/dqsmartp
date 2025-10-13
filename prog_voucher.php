<?php
    include 'program/koneksi.php';

    $loop = $_POST['totalvoucher'];
    $ket = $_POST['keterangan'];
    $i = 1;
    $err = '';
    $scc = 0;
    while ($i <= $loop)
    {
        $sql = "SELECT UUID() as UID, CONCAT(LEFT(UUID(), 4), '-', RIGHT(UUID(), 4), '-', SUBSTRING(UUID(), 5, 4)) AS ID";
        $result = $conn->query($sql);
        $hasil = 0;
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $hasil = $row['ID'];
                $sql = "INSERT INTO `VOUCHERS` (VOUCHER, STATE, CREATED_AT, KETERANGAN) VALUES ('$hasil', 'N', NOW(), '$ket')";

                if ($conn->query($sql) === TRUE) {
                    $scc++;
                } else {
                    $err = $conn->error;
                    header("location: voucher.php?scc='$scc'&error='$err'");
                    exit();
                }
            }
        } else {
            echo "0 results";
        }
        $i++;
    }
header("location: voucher.php?scc='$scc'&stat=succ'");
exit();