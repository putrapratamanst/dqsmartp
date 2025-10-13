<?php
session_start();
include "program/koneksi.php";
$sql = "UPDATE account SET STATE = 'ujian' WHERE ID = " . $_GET['id'];
echo $sql;
if ($conn->query($sql) === TRUE) {
    unlink("upload/$gambar");
    header("location: admin.php?sukses=User berhasil di aktivasi");
} else {
    $err = $conn->error;
    header("location: admin.php?error=User gagal di aktivasi");
    exit();
}
?>