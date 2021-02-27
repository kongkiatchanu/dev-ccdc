<?php
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
set_time_limit(0);
require_once("/var/www/html/app/webroot/api/hotspot/function.php");
require_once("/var/www/html/app/webroot/api/hotspot/modis_function.php");
$result = get_un_process();
$loop = 0;
while ($row = mysql_fetch_array($result)) {
	//print_r($row);
	$modis_id = $row["viirs_id"];
	$latitude = $row["latitude"];
	$longitude = $row["longitude"];
	if (distance_cnx($latitude, $longitude) <= 700) {
		echo $modis_id;
		//$meta = process_modis_meta($modis_id, $latitude, $longitude);
		$json_content = get_modis_json_meta($modis_id, $latitude, $longitude);
		save_modis_meta($modis_id, $json_content);
	}
	//print_r($meta);
	//echo "<br/>\n";
	//$loop++;
	//if ($loop > 5) {break;}
}
?>