<?php
require_once("function.php");
require_once("./lib/gpointconverter.class.php");

	$sql="SELECT modis_log.*, province.PROVINCE_NAME FROM modis_log 
		left join province on modis_log.log_province_id = province.PROVINCE_ID
		where province.GEO_ID = 1
		ORDER BY log_lastupdate ASC limit 1";
	$result = mysql_query($sql, $conn);
	$row = mysql_fetch_array($result);
	
	
	
	if(time() - strtotime($row["log_lastupdate"]) > 60*60*6){
		$province_id 	=  $row["log_province_id"];
		$period 		= array(24,48);
		$province 		= trim($row["PROVINCE_NAME"]);
		$country 		= "1";

		$gpoint = new GpointConverter();
		
		for($ii=0; $ii<=1; $ii++){
			
			$zindex = 1;

			$sql = "
				SELECT modis.*,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime, modis_meta.raw_json
				FROM modis
				LEFT JOIN modis_meta
				ON modis.viirs_id=modis_meta.modis_id
				WHERE (((CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR)) BETWEEN (NOW() - INTERVAL ".$period[$ii]." HOUR) AND NOW())
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
			$filename = $province_id.'result'.$period[$ii].'.json';
			
			//open or create the file
			$handle = fopen('/home/dev/public_html/assets/api/hotspot/temp/'.$filename,'w+');

			//write the data into the file
			fwrite($handle,$formattedData);

			//close the file
			fclose($handle);
			
			chmod('./temp/'.$filename, 0777);
			$error = false;
			
			$tmp = file_get_contents('./temp/'.$filename);
			if(strlen($tmp)>0){
				$sqlupate = "UPDATE hotspot.modis_log SET log_lastupdate = NOW(), log_status = 'complate' WHERE modis_log.log_province_id = {$province_id};";
				$result = mysql_query($sqlupate, $conn);
				
				echo 'update '.$filename.'-'.$province.'<br/>';
			}


		}

	}
	
?>
<META HTTP-EQUIV="Refresh" CONTENT="5;URL=gen_file.php">