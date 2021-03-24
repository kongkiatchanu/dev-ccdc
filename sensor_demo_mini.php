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

$conn = mysql_connect("localhost", "dev", "liveboxit");
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}

if($pm2_5=="" || $pm2_5==0) {
    echo "no pm2_5";
	exit;
}//aun
else {

    echo "ccdc be insterted.";
	mysql_select_db("dev", $conn);
	$sql = "INSERT INTO `log_mini_2561` (`log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`, `log_datetime`, `source_id`) VALUES ($pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\", FROM_UNIXTIME($ts), $webid)";
	$res = mysql_query($sql, $conn);
	// echo "insert result = " . print_r($res, 1) . "\r\n";

	// echo "sql=".print_r($sql, 1);
	mysql_close($conn);

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
