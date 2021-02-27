<?php
/*
ftp://nrt1.modaps.eosdis.nasa.gov/FIRMS/
u: thaidevelopers
p: Nasa1234
*/
set_time_limit(0);
require_once("modis_function.php");
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

$dir_list = ftp_nlist($conn_id, '-rtla /FIRMS/SouthEast_Asia/');

for ($loop=0; $loop<sizeof($dir_list); $loop++) {
	$current = $dir_list[$loop];
	$column = explode(" ", $current);
	$filename = trim($column[sizeof($column)-1]);
	$remote_size = ftp_size($conn_id, "/FIRMS/SouthEast_Asia/".$filename);
	$local_size = (filesize("./raw_modis/".$filename)) + 1;
	if ($remote_size > $local_size) {
		echo $remote_size.">".$local_size;
		echo "\n";
		unlink("./raw_modis/".$filename);
		save_modis($filename);
	}
}

// close the connection
ftp_close($conn_id);
?>