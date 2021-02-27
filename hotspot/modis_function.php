<?php
function save_modis($filename) {

	if (!file_exists("./raw_modis/".$filename)) {

		$conn = mysql_connect("localhost", "root", "lbcmsuccess");
		mysql_query("SET NAMES utf8", $conn);
		mysql_select_db("hotspot", $conn);

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
//		if (sizeof($lines) > 20) {
			file_put_contents("./raw_modis/".$filename, $raw);
//		}

	}
}

function get_un_process() {

		$conn = mysql_connect("localhost", "root", "lbcmsuccess");
		mysql_query("SET NAMES utf8", $conn);
		mysql_select_db("hotspot", $conn);
		
/*		$sql = "
			SELECT *,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime
			FROM modis
			LEFT JOIN modis_meta
			ON modis.viirs_id=modis_meta.modis_id
			WHERE modis_meta.modis_id IS NULL
			AND modis.acq_date='2016-04-05'
			AND latitude LIKE '18%'
		";*/

		$sql = "
			SELECT *,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime
			FROM modis
			LEFT JOIN modis_meta
			ON modis.viirs_id=modis_meta.modis_id
			WHERE modis_meta.modis_id IS NULL
			AND ((CAST(modis.acq_date AS DATE)) BETWEEN DATE_FORMAT(NOW() - INTERVAL 1 DAY, '%Y-%m-%d') AND DATE_FORMAT(NOW(), '%Y-%m-%d'))
		";
		
		$result = mysql_query($sql, $conn);
		return $result;
}

function get_modis_json_meta($modis_id, $latitude, $longitude) {
	//$json_content = trim(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?language=th&latlng=".$latitude.",".$longitude."&sensor=false&key=AIzaSyDR8HOgNdg-TmLRquiQxHZ8Pa4XEhJZdJ0"));
	$json_content = trim(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?language=th&latlng=".$latitude.",".$longitude."&sensor=false&key=AIzaSyAiyWcsxalFVCoFgnLVLf7_z5HUwKvPI0Q"));
	//AIzaSyAiyWcsxalFVCoFgnLVLf7_z5HUwKvPI0Q//Backup
	return $json_content;
}
	
function process_modis_meta($modis_id, $latitude, $longitude) {
	$meta = array();
	//$json_content = trim(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?language=th&latlng=".$latitude.",".$longitude."&sensor=false&key=AIzaSyDR8HOgNdg-TmLRquiQxHZ8Pa4XEhJZdJ0"));
	$json_content = trim(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?language=th&latlng=".$latitude.",".$longitude."&sensor=false&key=AIzaSyAiyWcsxalFVCoFgnLVLf7_z5HUwKvPI0Q"));
	//AIzaSyAiyWcsxalFVCoFgnLVLf7_z5HUwKvPI0Q//Backup
	//echo $json_content;
	$array_geocode = (array)json_decode($json_content);
	$array_results = (array)($array_geocode[results][0]);

	$meta[address] = $array_results[formatted_address];
	
	return $meta;
}

function save_modis_meta($modis_id, $json_content) {
/*
meta_id
modis_id
raw_json
address
tambon
amphur
province
country
th_datetime
*/
	$conn = mysql_connect("localhost", "root", "lbcmsuccess");
	mysql_query("SET NAMES utf8", $conn);
	mysql_select_db("hotspot", $conn);
	
	$sql = "INSERT IGNORE INTO modis_meta (modis_id, raw_json) VALUES ('$modis_id', '$json_content')";
	mysql_query($sql, $conn);
	
}
?>