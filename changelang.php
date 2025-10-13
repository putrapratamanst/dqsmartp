<?php
session_start();
$lang = isset($_GET['lang'])?$_GET['lang']:'id';
$_SESSION['lang'] = $lang;
header("location: index.php");
exit();
