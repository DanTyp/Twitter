<?php

$servername = 'localhost';
$userName = 'root';
$password = 'CodersLab';
$dbname = 'twitter';

$connection = new mysqli($servername, $userName, $password, $dbname);

if($connection->connect_error){
    die("Connection to database twitter failed: $connection->connect_error");
}

//echo "Connection to database $dbname successful!<br>";