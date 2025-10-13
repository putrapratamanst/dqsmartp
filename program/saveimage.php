<?php
session_start();

$img = $_POST['gambar'];

$imageDataEncoded = $_POST['gambar'];
$data = explode( ',', $imageDataEncoded );
$imageData = base64_decode($data[1]);
$imageName2 = "../chart/" . $_SESSION['ID'] . 's.jpg';
$imageName = "../chart/" . $_SESSION['ID'] . 's.png';
$_SESSION['gambar'] = "chart/" . $_SESSION['ID'] . 's.jpg';
$success = file_put_contents($imageName, $imageData);
$source = imagecreatefromstring($imageData);
$angle = 0;
//$transColor = imagecolorallocate($source, 255, 255, 255);

//$targetImg = imagecreatetruecolor(400, 400);
//$white = imagecolorallocate($targetImg, 255, 255, 255);
//imagefill( $targetImg, 0, 0, $white );
//$rotate = imagefill( $source, 0, 0, $white );
//$imageSave = imagejpeg($rotate,$imageName2,100);


$input_file = $imageName;
$output_file = $imageName2;

$input = imagecreatefrompng($input_file);
$width = imagesx($input);
$height = imagesy($input);
$output = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($output,  255, 255, 255);
imagefilledrectangle($output, 0, 0, $width, $height, $white);
imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
imagejpeg($output, $output_file);
?>