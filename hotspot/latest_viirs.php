<?php
/*
ftp://nrt1.modaps.eosdis.nasa.gov/FIRMS/
u: thaidevelopers
p: Nasa1234
*/
$ftp_server = "nrt1.modaps.eosdis.nasa.gov";
$ftp_user_name = "thaidevelopers";
$ftp_user_pass = "Nasa1234";

// set up a connection or die
$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 

// try to login
if (@ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
    echo "Connected as $ftp_user_name@$ftp_server\n";
} else {
    echo "Couldn't connect as $ftp_user_name\n";
}

ftp_pasv($conn_id, true);

$dir_list = ftp_nlist($conn_id, '-rtla /FIRMS/viirs/SouthEast_Asia/');

krsort($dir_list);

$last_array = $dir_list[sizeof($dir_list)-1];
$column = explode(" ", $last_array);
$filename = trim($column[sizeof($column)-1]);



// close the connection
ftp_close($conn_id);
include("viirs.php");
?>