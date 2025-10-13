<?php
include 'program/koneksi.php';
$id = $_POST['id'];
$question = $_POST['question'];
$sql = "UPDATE `ANSWER` SET ANSWER = '$question' WHERE ID = $id";

if ($conn->query($sql) === TRUE) {
    header("location: answer.php?success=success");
    exit();
} else {
    $err = $conn->error;
    header("location: answer.php?error=$err");
    exit();
}