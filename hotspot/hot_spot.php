<?php
require_once("function.php");
	
	date_default_timezone_set("Europe/London");
	
	echo $_GET['callback'];

	if($_GET["filename"]!=""){

			
			echo "(";
				$sql="SELECT modis.acq_date, count(modis.viirs_id) as hotspot 
				FROM modis 
				LEFT JOIN modis_meta ON modis.viirs_id=modis_meta.modis_id 
				where (acq_date BETWEEN '2017-01-01' AND '".date('Y-m-d')."') 
				AND modis_meta.raw_json LIKE '%ประเทศไทย%' 
				AND modis_meta.raw_json LIKE '%".$_GET["filename"]."%' 
				group by modis.acq_date ORDER BY modis.acq_date ASC";

				$result = mysql_query($sql, $conn);
				$data = array();
				while($rs = mysql_fetch_array($result))
				{
					$day_array = array(strtotime($rs["acq_date"]) *1000, $rs["hotspot"]);
					array_push($data,$day_array);
				}
				echo json_encode($data, JSON_NUMERIC_CHECK);
					
			echo ");";
			
	}
?>
