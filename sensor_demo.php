<?php

$pm10 = $_POST['pm10'];
$pm2_5 = $_POST['pm2_5'];
$secret = $_POST['secret'];
$station = $_POST['id'];
$temp = $_POST['temp'];
$humid = $_POST['humid'];
$nickname = $_POST['nickname'];
$src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];


//TOM-DEBUG
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $src_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
file_put_contents('/home/dev/public_html/assets/api/logs/post_aun.log', date("Y-m-d H:i:s") . "|" . $src_ip . "|" . var_export($_POST, true) . "\n", FILE_APPEND);
//END-TOM-DEBUG


if (!$temp)
  $temp = "NULL";
if (!$humid)
  $humid = "NULL";

if ($secret != 'e96cfe7eb8b48d6b5c492de81383275fce7a8bc743eccde62c6efce7aacba1e9') {
  die('access denied.');
}

$mysqli = new mysqli("localhost", "dev", "liveboxit", "dev");

if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli->connect_error;
  exit();
}

if ($pm2_5 == "" || $pm2_5 == 0) {
  exit;
} //aun
else {

  $sql_temp = "INSERT INTO `log_zdata` (`source_id`, `log_pm1`, `log_pm10`, `log_pm25`, `temp`, `humid`, `wind_speed`, `wind_direction`, `atmospheric`, `source_ip`) VALUES ($station, NULL, $pm10, $pm2_5, $temp, $humid, NULL, NULL, NULL, \"$src_ip\")";
  $res3 = $mysqli->query($sql_temp);

  $sql = "INSERT INTO `log_data_2561` (`source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, `source_ip`) VALUES ($station, $pm10, $pm2_5, $temp, $humid, \"$src_ip\")";
  $res = $mysqli->query($sql);

  $sql2 = "INSERT INTO `log_data_2562` (`source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`) VALUES ($station, $pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\")";
  file_put_contents('/home/dev/public_html/assets/api/logs/post_sql.log', date("Y-m-d H:i:s") . "|" . $sql2 .  "\n", FILE_APPEND);
  $res2 = $mysqli->query($sql2);

  


  // if (!$res) {
  //     // echo "FAILED: " . $sql . "\n";
  //     //$message  = 'Invalid query: ' . mysql_error() . "\n";
  //     $message .= 'Whole query: ' . $sql . "\n";
  //     echo $message;
  // }
  // else {
  //     echo "OK: " . $sql . "\n";
  // }




  //mysql_close($conn);
  // echo "$POST=" . print_r(json_encode($_POST), 1);
  //    print_r($_SERVER);
  // code($_POST), 1);
}
//    print_r($_SERVER);
/*
}
else {
	echo "pm10 or pm2.5 = 0";
	echo "$POST=" . print_r(json_encode($_POST), 1);

}
*/
?>