<?php
include 'program/koneksi.php';
$id = $_POST['id'];
$question = $_POST['question'];
$sql = "UPDATE `QUESTION` SET QUESTION = '$question' WHERE ID = $id";

if ($conn->query($sql) === TRUE) {
    header("location: question.php?success=success");
    exit();
} else {
    $err = $conn->error;
    header("location: question.php?error=$err");
    exit();
}