<?php

function getBgColor($nilai) {
    $warna = "bg-grade-d"; //"#c40010";
    if($nilai > 115) {
        $warna = "bg-grade-a"; // "#260e83";
    } elseif($nilai >= 100) {
        $warna = "bg-grade-b"; // "#f58a0a";
    } elseif($nilai >= 85) {
        $warna = "bg-grade-c"; // "#ed2207";
    }
    
    return $warna;
}