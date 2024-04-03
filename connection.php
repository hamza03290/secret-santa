<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "secretsanta";

if (!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
    die("connectiefout");
}
