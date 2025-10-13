<?php
//session_destroy();
//if (isset($_SESSION['ID'])) {
session_start();
unset($_SESSION['ID']);
//}
header("location: index.php");
exit();
