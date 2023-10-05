<?php

 $pm10 = $_POST['pm10'];
 $pm2_5 = $_POST['pm2_5'];
 $secret = $_POST['secret'];
 $station = $_POST['id'];
 $temp = $_POST['temp'];
 $humid = $_POST['humid'];
 $ts = $_POST['timestamp'];
 $dt = $_POST['dt'];
 $webid = $_POST['webid'];
 $nickname = $_POST['nickname'];
 $src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

if (!$temp) $temp = "NULL"  ;
if (!$humid) $humid = "NULL";
    echo $secret;
if ($secret != 'e96cfe7eb8b48d6b5c492de81383275fce7a8bc743eccde62c6efce7aacba1e9') {
  die('access denied. '. print_r($_POST, 1));
}

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
	$src_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
file_put_contents('/home/dev/public_html/assets/api/logs/demo_mini.log', date("Y-m-d H:i:s") . "|" . $src_ip . "|" . var_export($_POST, true) . "\n", FILE_APPEND);


$mysqli = new mysqli("localhost","dev","liveboxit","dev");

    // Check connection
    if ($mysqli -> connect_errno) {
      echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
      exit();
    }

if($pm2_5=="" || $pm2_5==0) {
    echo "no pm2_5";
	exit;
}//aun
else {

	$sql2 = "INSERT INTO `log_zdata` (`log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`, `log_datetime`, `source_id`) VALUES ($pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\", FROM_UNIXTIME($ts), $webid)";
	$res2 = $mysqli->query($sql2);

   
	$sql = "INSERT INTO `log_mini_2561` (`log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`, `log_datetime`, `source_id`) VALUES ($pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\", FROM_UNIXTIME($ts), $webid)";
	$res = $mysqli->query($sql);
	// echo "insert result = " . print_r($res, 1) . "\r\n";

	// echo "sql=".print_r($sql, 1);
	
	
     echo "$POST=" . print_r(json_encode($_POST), 1);
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
