<?php
session_start();
include "program/koneksi.php";
$sql = "SELECT * FROM `IMAGE` WHERE USERID = " . $_SESSION['ID'] . " ORDER BY UPLOAD_AT";
echo "SQL : " . $sql . "</br>";
$result = $conn->query($sql);
echo "Result succ </br>";
$gambar = '';

echo "Koneksi builded</br>";
if ($result->num_rows > 0) {
    // output data of each row
    echo "Result more than zero</br>";
    while($row = $result->fetch_assoc()) {
        $gambar = $row['IMAGE'];
        $sql = "DELETE FROM IMAGE WHERE USERID = " . $_SESSION['ID'];
        echo $sql;
        if ($conn->query($sql) === TRUE) {
            unlink("upload/$gambar");
            header("location: upload.php?sukses=File berhasil di hapus");
        } else {
            $err = $conn->error;
            header("location: upload.php?error=Upload gambar gagal");
            exit();
        }
    }
} else {
    echo "0 results";
}


exit();