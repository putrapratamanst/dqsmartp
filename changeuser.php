<?php
session_start();
include "program/koneksi.php";
$_SESSION['ID'] = $_GET['id'];
echo $_GET['id'] . "<br>";
$sql = "SELECT * FROM `account` WHERE ID = " . $_GET['id'] . " order by ID DESC";
        $result = $conn->query($sql);
        $username = '';
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
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