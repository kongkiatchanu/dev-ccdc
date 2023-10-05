<?php 
error_reporting(-1);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
$conn = mysql_connect("localhost", "dev", "liveboxit");
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}

echo 'hi';