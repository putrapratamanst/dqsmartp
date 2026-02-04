<?php

//session_start();

include 'program/koneksi.php';

$ID = $_SESSION['ID'];
// Check if result already exists to prevent duplicate submissions
$sql_check = "SELECT COUNT(*) as cnt FROM `RESULT` WHERE USERID = '$ID'";
$result_check = $conn->query($sql_check);
$row_check = $result_check->fetch_assoc();
if ($row_check['cnt'] > 0) {
    header("Location: result.php");
    exit;
}


$ans = explode("L",$_GET['answer']);

$answer = '';

$ix = 1;

$err = 0;

//include 'program/koneksi.php';



foreach($ans as $is =>$key) {

    $ques = $is + 1;

    $sql2 = "SELECT * FROM `QUESTION` WHERE ID = $ix";

    $result2 = $conn->query($sql2);

        while($row2 = $result2->fetch_assoc()) {

            if ($row2['NILAI'] == 1)

            {

                $sql = "SELECT @rownum:=@rownum+1 AS rownum, A.* FROM `ANSWER` AS A, (SELECT @rownum:=0) AS t WHERE QUESTION = $ix ORDER BY ID";

                $result = $conn->query($sql);
                
                $res = 0;

                if ($result->num_rows > 0) {

                    // output data of each row

                    while($row = $result->fetch_assoc()) {

                        if($row['rownum'] == $key)

                        {

                            $res = $row['POINT'];

                            $sql = "INSERT INTO `RESULT` (USERID, QUESTION, ANSWER, ACTIVITY_ON, VALUE) VALUES ($ID, $ques, $key, NOW(), $res)";

                            if ($conn->query($sql) === TRUE) {

                                $scc++;

                            } else {

                                $err = 1;

                                echo "<script>alert('Error : $err');</script>";

                                echo "<script>alert('SQL : $sql');</script>";

                            }

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

                $exs = explode("X", $key);

                $sql = "SELECT @rownum:=@rownum+1 AS rownum, A.* FROM `ANSWER` AS A, (SELECT @rownum:=0) AS t WHERE QUESTION = $ix ORDER BY ID";

                $result = $conn->query($sql);

                $res = 0;

                if ($result->num_rows > 0) {

                    // output data of each row

                    while($row = $result->fetch_assoc()) {

                        $ind = $row['rownum'] - 1;

                        if($row['rownum'] == $exs[$ind])

                        {

                            $res = $row['POINT'];

                            $sql = "INSERT INTO `RESULT` (USERID, QUESTION, ANSWER, ACTIVITY_ON, VALUE) VALUES ($ID, $ques, " . $exs[$ind] . ", NOW(), $res)";

                            if ($conn->query($sql) === TRUE) {

                                $scc++;

                            } else {

                                $err = 1;

                                echo "<script>alert('Error : $err');</script>";

                                echo "<script>alert('SQL : $sql');</script>";

                            }

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

    $sql = "UPDATE `account` SET STATE = 'FINISH' WHERE ID = " . $ID;


    if ($conn->query($sql) === TRUE) {

        header("location: result.php");

        exit();

    } else {

        $err = $conn->error;

        echo "<script>alert('Error : $err');</script>";

    }

}

else

{

    echo "Error";

}