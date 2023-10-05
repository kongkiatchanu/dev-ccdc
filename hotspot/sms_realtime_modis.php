<?php
require_once("function.php");
require_once("./lib/gpointconverter.class.php");
$total_hotspot = 0;
$last_date = "";
$last_time = "";
$last_datetime = "";

$satellite_name = array("T" => "Terra", "A" => "Aqua");

//init value
$period = "24";
$province = "เชียงใหม่";
$country = "1";

$gpoint = new GpointConverter();

	$sql = "
		SELECT modis.*,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime, modis_meta.raw_json
		FROM modis
        LEFT JOIN modis_meta
        ON modis.viirs_id=modis_meta.modis_id
		WHERE (((CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR)) BETWEEN (NOW() - INTERVAL ".$period." HOUR) AND NOW())
		AND raw_json LIKE '%".$province."%' 
		AND raw_json LIKE '%ประเทศไทย%' 
		ORDER by acq_datetime	

	";
	
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_array($result)) {
	
	/* latitude, longitude, brightness, scan, track, acq_date, acq_time, satellite,bright_t31, frp */

		$latitude = $row["latitude"];
		$longitude = $row["longitude"];
		$utm_result = $gpoint->convertLatLngToUtm($latitude, $longitude);
		$UTMEasting = $utm_result[0];
		$UTMNorthing = $utm_result[1];
		$UTMZone = $utm_result[2];
		$acq_datetime = $row["acq_datetime"];
		$satellite = $satellite_name[trim($row["satellite"])];
		$bright_t31 = $row["bright_t31"];
		$frp = $row["frp"];
		$raw_json = trim($row["raw_json"]);
		$gg = (array)json_decode($raw_json);	
		$results = (array)$gg['results'][0];	
		$formatted_address = $results['formatted_address'];
		$last_datetime = $row["acq_datetime"];
		$total_hotspot++;
	}
	
	$sql = "
		SELECT (CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS latest_datetime
		FROM modis
		ORDER BY 1 DESC LIMIT 0,1	
	";
	$result = mysql_query($sql, $conn);
	$row = mysql_fetch_array($result);
	$latest_datetime = $row["latest_datetime"];
	
	$sms_text = "Past 24 hours, Chiang Mai has ".$total_hotspot." hotspot(s). Last updated from NASA at ".$latest_datetime.". More detail please visit http://tinyurl.com/zea322c";
	echo $sms_text;
	include("beepbeepccdc.php");
?>