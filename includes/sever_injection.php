<?php

$mystring = strtoupper($_SERVER['QUERY_STRING']);
$server_injec1 = strpos($mystring, 'SELECT');
$server_injec2 = strpos($mystring, 'UNION');

if (($server_injec1 === false) && ($server_injec2 === false) || ($server_injec1 === '0') && ($server_injec2 === '0')) {
    ;
}//end if
else {
    header('location:index.php');
    exit();
}//end else
?>