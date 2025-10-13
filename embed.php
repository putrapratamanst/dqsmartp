<?php

$userid = 'admin';

if ($userid) {

    echo 'debug: Authenticated on tableau as ', $userid, '<br />';

    $loginparams = 'username='. $userid;
    
    $urlparams = ':embed=yes&:toolbar=no';

    $url = 'https://34.101.65.166/trusted';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,$url);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS,$loginparams);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    
    echo 'Curl init<br>';
    echo $ch . "<br>";
    echo curl_exec($ch) . '<br>';
    $ticket = curl_exec($ch);

    curl_close ($ch);
    echo 'curl closed<br>';
    echo $ticket;

} else {

    echo 'Log on to see Tableau content';

}

?>