<?php
session_start();
$uploaded = $_POST['uploaded'];
$id = $_SESSION['ID'];

$target_dir = "upload/";
$target_file = $target_dir . $id.$_FILES["uploaded"]["name"];
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["uploaded"]["tmp_name"]);
if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
} else {
    echo "File is not an image. [" . $_FILES["uploaded"]["tmp_name"] . " - $uploaded]";
    header("File is not an image. [" . $_FILES["uploaded"]["tmp_name"] . "]");
    exit();
}

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    header("Sorry, file already exists.");
    exit();
}

// Check file size
if ($_FILES["uploaded"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    header("Sorry, your file is too large.");
    exit();
}


// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["uploaded"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["uploaded"]["name"])). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
include 'program/koneksi.php';
$uploaded = $id.$_FILES["uploaded"]["name"];
$sql = "INSERT INTO IMAGE (IMAGE, USERID, UPLOAD_AT) VALUES ('$uploaded', '$id', NOW())";
echo $sql;
if ($conn->query($sql) === TRUE) {
    header("location: upload.php?sukses=Gambar berhasil di upload");
    exit();
} else {
    $err = $conn->error;
    header("location: upload.php?error=Upload gambar gagal");
    exit();
}
?>