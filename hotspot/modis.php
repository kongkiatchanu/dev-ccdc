<?php
$conn = mysql_connect("localhost", "root", "lbcmsuccess");
mysql_select_db("hotspot", $conn);

/*	$sql = "DELETE FROM modis";
	mysql_query($sql, $conn);*/

//$raw = trim(file_get_contents("raw/modis/SouthEast_Asia_MCD14DL_2016093.txt"));
//echo "ftp://thaidevelopers:Nasa1234@nrt1.modaps.eosdis.nasa.gov/FIRMS/SouthEast_Asia/".$filename;exit();
$raw = trim(file_get_contents("ftp://thaidevelopers:Nasa1234@nrt1.modaps.eosdis.nasa.gov/FIRMS/SouthEast_Asia/".$filename));
echo "ftp://thaidevelopers:Nasa1234@nrt1.modaps.eosdis.nasa.gov/FIRMS/SouthEast_Asia/".$filename;
$lines = explode("\n", $raw);
for ($loop=1; $loop<sizeof($lines);$loop++) {
	$row = explode(",", $lines[$loop]);
	//latitude,longitude,brightness,scan,track,acq_date,acq_time,satellite,confidence,version,bright_t31,frp
	$latitude = $row[0];
	$longitude = $row[1];
	$brightness = $row[2];
	$scan = $row[3];
	$track = $row[4];
	$acq_date = $row[5];
	$acq_time = $row[6];
	$satellite = $row[7];
	$confidence = $row[8];
	$version = $row[9];
	$bright_t31 = $row[10];
	$frp = $row[11];
	
	$sql = "INSERT IGNORE INTO modis (latitude, longitude, brightness, scan, track, acq_date, acq_time, satellite, confidence, version, bright_t31, frp) VALUES ('$latitude', '$longitude', '$brightness', '$scan', '$track', '$acq_date', '$acq_time', '$satellite', '$confidence', '$version', '$bright_t31', '$frp')";
	mysql_query($sql, $conn);
}

?>