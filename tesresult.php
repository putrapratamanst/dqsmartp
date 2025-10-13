<?php
session_start();
$ID = $_SESSION['ID'];
$ans = explode("L",$_GET['answer']);
$answer = '';
$ix = 1;
$err = 0;
include 'program/koneksi.php';
echo "Running"; echo "<br>";
foreach($ans as $is =>$key) {
    $ques = $is + 1;
    echo "Running - $ix";echo "<br>";
    $sql2 = "SELECT * FROM `QUESTION` WHERE ID = $ix";
    $result2 = $conn->query($sql2);
        while($row2 = $result2->fetch_assoc()) {
            if ($row2['NILAI'] == 1)
            {
                echo "Soal $ix - Type " . $row2['NILAI']; echo "<br>";
                $sql = "SELECT @row_number:=@row_number+1 AS row_number, A.* FROM `ANSWER` AS A, (SELECT @row_number:=0) AS t WHERE QUESTION = $ix ORDER BY ID";
                $result = $conn->query($sql);
                $res = 0;
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        if($row['row_number'] == $key)
                        {
                            $res = $row['POINT'];
                            $sql = "INSERT INTO `RESULT` (USERID, QUESTION, ANSWER, ACTIVITY_ON, VALUE) VALUES ($ID, $ques, $key, NOW(), $res)";
                            echo $sql;echo "<br>"; 
                        }
                    }
                }
                else
                {
                    $err = 1;
                    echo "0 Results<br>";
                    echo $_GET['answer'];
                }

            }
            else
            {
                echo "Soal $ix - Type " . $row2['NILAI']; echo "<br>";
                $exs = explode("X", $key);
                $sql = "SELECT @row_number:=@row_number+1 AS row_number, A.* FROM `ANSWER` AS A, (SELECT @row_number:=0) AS t WHERE QUESTION = $ix ORDER BY ID";
                $result = $conn->query($sql);
                $res = 0;
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        $ind = $row['row_number'] - 1;
                        if($row['row_number'] == $exs[$ind])
                        {
                            $res = $row['POINT'];
                            $sql = "INSERT INTO `RESULT` (USERID, QUESTION, ANSWER, ACTIVITY_ON, VALUE) VALUES ($ID, $ques, " . $exs[$ind] . ", NOW(), $res)";
                            echo $sql;echo "<br>";
                        }
                    }
                }
                else
                {
                    $err = 1;
                    echo "0 Results<br>";
                    echo $_GET['answer'];
                }
            }
        }

    $ix++;
}

if ($err == 0)
{
    
}
else
{
    echo "Error";
}