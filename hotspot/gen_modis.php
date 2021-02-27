<?php
require_once("function.php");
require_once("./lib/gpointconverter.class.php");
$total_hotspot = 0;
$last_date = "";
$last_time = "";
$last_datetime = "";

$satellite_name = array("T" => "Terra", "A" => "Aqua");

//init value
$period = "48";
$province = "เชียงใหม่";
$country = "1";



$sql="SELECT * FROM `province` WHERE `PROVINCE_NAME` LIKE '%เชียงใหม่%' ORDER BY `PROVINCE_NAME` ASC";
$result = mysql_query($sql, $conn);
$row = mysql_fetch_array($result);
$set_province_id = $row["PROVINCE_ID"];



$gpoint = new GpointConverter();



	$zindex = 1;

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
	$temp = array();
	while ($row = mysql_fetch_array($result)) {
	
	

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
			
			$data[$zindex] = array('hotspot'.$zindex, $latitude, $longitude, $zindex, $acq_datetime, $satellite, $bright_t31, $frp, $UTMEasting, $UTMNorthing, $UTMZone, $formatted_address);
			
			$last_datetime = $row["acq_datetime"];
			$total_hotspot++;
			
			array_push($temp,$data[$zindex]);
		$zindex++;
		
		
	}
	
	
	//format the data
	$formattedData = json_encode($temp);

	//set the filename
	$filename = $set_province_id.'result'.$period.'.json';

	//open or create the file
	$handle = fopen('/home/dev/public_html/assets/api/hotspot/temp/'.$filename,'w+');

	//write the data into the file
	fwrite($handle,$formattedData);

	//close the file
	fclose($handle);
	echo $filename;
	
	$error = false;
	
	$tmp = file_get_contents('./temp/'.$filename);
	echo " | File size: ".strlen($tmp)."<br />";
	if(strlen($tmp) > 0) {
			
		}
	else {
			$error = true;
		}
	

	echo $error ? "Error" : "Complete";
?>
      