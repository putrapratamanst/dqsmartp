<?php
session_start();
$pick = $_GET['pick'];
$no = $_GET['no'] - 1;
$type = $_GET['type'];
if ( $_GET['no'] == 47)
{
    $next = 48;
}
else
{
    $next = $_GET['no'] + 1;
}
$tes = $_GET['answer'];
$ans = explode("L",$_GET['answer']);
$answer = '';
$ix = 0;
if ($type == 2)
{
    $nox = 1;
    $picky = explode("X", $ans[$no]);
    foreach ($picky as $iks => $kuy)
    {
        if ($ix == 0)
        {
            if ($nox == $pick)
            {
                $answer = $pick;
            }
            else
            {
                $answer = $kuy;
            }
        }
        else
        {
            if ($nox == $pick)
            {
                $answer = $answer . 'X' . $pick;
            }
            else
            {
                $answer = $answer . 'X' . $kuy;
            }
        }
        $nox++;
        $ix++;
    }
    $pick = $answer;
}
else
{
    $pick = $_GET['pick'];
}
$answer = '';
$ix = 0;
foreach($ans as $is =>$key) {
    if ($ix == 0)
    {
        if ($no == $ix)
        {
            $answer = $pick;
        }
        else
        {
            $answer = $key;
        }
    }
    else
    {
        if ($no == $ix)
        {
            $answer = $answer . 'L' . $pick;
        }
        else
        {
            $answer = $answer . 'L' . $key;
        }
    }
    $ix++;
}
if($type == 1)
{
    header("location: quiz.php?no=$next&answer=$answer&pick=$pick");
}
else
{
    $next = $next - 1;
    header("location: quiz.php?no=$next&answer=$answer&pick=$pick");
}
exit();

