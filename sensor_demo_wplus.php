<?php
 $pm10 = $_POST['pm10'];
 $pm2_5 = $_POST['pm2_5'];
 $secret = $_POST['secret'];
 $station = $_POST['id'];
 $temp = $_POST['temp'];
 $humid = $_POST['humid'];
 $nickname = $_POST['nickname'];
 //add
 $pm1 = $_POST['pm1'];
 $wind_speed = $_POST['wind_speed'];
 $wind_direction = $_POST['wind_direction'];
 $atmospheric = $_POST['atmospheric'];
 $src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];


//TOM-DEBUG
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $src_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
file_put_contents('/home/dev/public_html/assets/api/logs/post_wplus.log', date("Y-m-d H:i:s")."|".$src_ip."|".var_export($_POST, true)."\n", FILE_APPEND);
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

 

	$sql = "INSERT INTO `log_wplus` (`source_id`, `log_pm1`, `log_pm10`, `log_pm25`, `temp`, `humid`, `wind_speed`, `wind_direction`, `atmospheric`, `source_ip`) VALUES ($station, $pm1, $pm10, $pm2_5, $temp, $humid, $wind_speed, $wind_direction, $atmospheric, \"$src_ip\")";
	$sql_temp = "INSERT INTO `log_zdata` (`source_id`, `log_pm1`, `log_pm10`, `log_pm25`, `temp`, `humid`, `wind_speed`, `wind_direction`, `atmospheric`, `source_ip`) VALUES ($station, $pm1, $pm10, $pm2_5, $temp, $humid, $wind_speed, $wind_direction, $atmospheric, \"$src_ip\")";
	// echo "$sql";
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
	$res2 = $mysqli->query($sql_temp);
	

}
	

?>
