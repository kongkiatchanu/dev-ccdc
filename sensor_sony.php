<?php
	$payload = ''.$_POST['msdu'];
	$nickname = $_POST['lfourId'];
	$sony_payload = json_decode($_POST["sony_payload"]);
	$log_datetime = date('Y-m-d H:i:s',$sony_payload->txTime);
	
	$ck= substr($payload, -3);
	$P3_STR = substr($payload, 12, 18);
	$P4_STR = substr($payload, 2, 14);
	$P1_STR = substr($payload, 18, 24);
	$P2_STR = substr($payload, 24, 34);
	$P1 = hexdec($P1_STR);
	$P2 = hexdec($P2_STR);
	// Location
	$location = base_convert($P4_STR,16,2);
	if(strlen($location)<56){
		$f = 56 - strlen($location);
		for($i=0; $i<$f; $i++){
			$location = '0'.$location;
		}
	}
	$MILS2DEG = 1.0/(3600*1000);

	$rLat = base_convert(substr($location, 0, 25),2,10);
	$rLon = base_convert(substr($location, 25, 26),2,10);

	$lat = ($rLat * 32) * $MILS2DEG - 90.0;
	$lon = ($rLon * 32) * $MILS2DEG - 180.0;

	// TEMP
	$hexadecimal = substr($P1_STR,2,2);
	$bi = base_convert($hexadecimal,16,2);
	if(strlen($bi)<=7){
		$TEMP = hexdec($hexadecimal);
	}else{
		$TEMP = base_convert(substr($bi,1,7),2,10);
	}
	
	$CNTNUM = ($P1 >> 32) & 0xFF;
	$PM100 = ($P1 >> 22) & 0x3FF;
	$PM025 = ($P1 >> 12) & 0x3FF;

	//$TEMP = hexdec($hexadecimal);  # Temperature by BME280
	$humid = $P1 & 0x7F;  # Humidity by BME280
	$STAND = ($P1 >> 8) & 0x0F;  # Standing status by BMI160
	if($nickname=="94720"){$station = 2129;}
	else if($nickname=="94618"){$station = 2126;}
	else if($nickname=="94613"){$station = 2127;}
	else if($nickname=="94719"){$station = 2128;}
	else if($nickname=="94647"){$station = 2119;}
	else if($nickname=="94625"){$station = 2120;}
	else if($nickname=="94619"){$station = 2121;}
	else if($nickname=="94659"){$station = 2122;}
	else if($nickname=="94612"){$station = 2123;}
	else if($nickname=="94698"){$station = 2124;}
	else if($nickname=="94662"){$station = 2125;}
    

	$pm10 = $PM100;
	$pm2_5 = $PM025;
	$secret = $_POST['secret'];
	$temp = $TEMP;
    $standing = $STAND;
	$msdu = $_POST['msdu'];
	$src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];


//TOM-DEBUG
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $src_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
file_put_contents('/home/dev/public_html/assets/api/logs/post_sony.log', $log_datetime."|".date("Y-m-d H:i:s")."|".$src_ip."|".var_export($_POST, true)."\n", FILE_APPEND);
//END-TOM-DEBUG

if (!$temp) $temp = "NULL"  ;
if (!$humid) $humid = "NULL";
if (!$pm1) $pm1 = "NULL";
if (!$pm10) $pm10 = "NULL";

if ($secret != 'e96cfe7eb8b48d6b5c492de81383275fce7a8bc743eccde62c6efce7aacba1e9') {
  die('access denied.');
}

$mysqli = new mysqli("localhost","dev","liveboxit","dev");

    // Check connection
    if ($mysqli -> connect_errno) {
      echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
      exit();
    }

if($pm2_5=="" || $pm2_5==0) {
	exit;
}//aun
else {

	$sql = "INSERT INTO `log_sony` (`source_id`, `nickname`, `cid`, `lat`, `lon`, `log_pm1`, `log_pm10`, `log_pm25`, `temp`, `humid`, `standing`, `msdu`, `log_datetime`, `source_ip`) VALUES ($station, $nickname, $CNTNUM, $lat, $lon, $pm1, $pm10, $pm2_5, $temp, $humid, $standing, \"$payload\", \"$log_datetime\", \"$src_ip\")";
	//$sql_temp = "INSERT INTO `log_zdata` (`source_id`, `log_pm1`, `log_pm10`, `log_pm25`, `temp`, `humid`, `wind_speed`, `wind_direction`, `atmospheric`, `source_ip`) VALUES ($station, $pm1, $pm10, $pm2_5, $temp, $humid, $wind_speed, $wind_direction, $atmospheric, \"$src_ip\")";

	$res = $mysqli->query($sql);
        if (!$res) {
            // echo "FAILED: " . $sql . "\n";
            //$message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql . "\n";
            echo $message;
        }
        else {
            echo "OK: " . $sql . "\n";
        }
		
	//$sql2 = "INSERT INTO `log_zdata` (`source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`,`log_datetime`,`standing`) VALUES ($station, $pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\", \"$log_datetime\",$standing)";
	//$res2 = mysql_query($sql2, $conn);
	//$res2 = mysql_query($sql_temp, $conn);
	//mysql_close($conn);

}
	

?>
