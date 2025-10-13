<?php
session_start();
$answer = '';
$i = 0;

include "program/koneksi.php";
$sql = "SELECT * FROM `QUESTION`";
$result = $conn->query($sql);
$username = '';
if ($result->num_rows > 0) {
    // output data of each row
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        if ($no == 1)
        {
            if($row['NILAI'] == 1)
            {
                $answer = '0';
            }
            else
            {
                $sql2 = "SELECT * FROM `ANSWER` WHERE QUESTION = " . $row['ID'];
                $result2 = $conn->query($sql2);
                if ($result2->num_rows > 0) {
                    // output data of each row
                    $nox = 1;
                    while ($row2 = $result2->fetch_assoc()) {
                        if ($nox == 1)
                        {
                            $answer = '0';
                        }
                        else
                        {
                            $answer = $answer . 'X0';
                        }
                        $nox++;
                    }
                }
            }
        }
        else
        {
            if($row['NILAI'] == 1)
            {
                $answer = $answer . 'L0';
            }
            else
            {
                $sql2 = "SELECT * FROM `ANSWER` WHERE QUESTION = " . $row['ID'];
                $result2 = $conn->query($sql2);
                if ($result2->num_rows > 0) {
                    // output data of each row
                    $nox = 1;
                    while ($row2 = $result2->fetch_assoc()) {
                        if ($nox == 1)
                        {
                            $answer = $answer . 'L0';
                        }
                        else
                        {
                            $answer = $answer . 'X0';
                        }
                        $nox++;
                    }
                }
            }
        }
        $no++;
    }
}

header("location: quiz.php?no=1&answer=$answer");
exit();