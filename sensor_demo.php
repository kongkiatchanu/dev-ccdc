<?php

 $pm10 = $_POST['pm10'];
 $pm2_5 = $_POST['pm2_5'];
 $secret = $_POST['secret'];
 $station = $_POST['id'];
 $temp = $_POST['temp'];
 $humid = $_POST['humid'];
 $nickname = $_POST['nickname'];
 $src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

if (!$temp) $temp = "NULL"  ;
if (!$humid) $humid = "NULL";

if ($secret != 'e96cfe7eb8b48d6b5c492de81383275fce7a8bc743eccde62c6efce7aacba1e9') {
  die('access denied.');
}

$conn = mysql_connect("localhost", "dev", "liveboxit");
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}

if($pm2_5=="" || $pm2_5==0) {
	exit;
}//aun
else {


	mysql_select_db("dev", $conn);
	$sql = "INSERT INTO `log_data_2561` (`source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, `source_ip`) VALUES ($station, $pm10, $pm2_5, $temp, $humid, \"$src_ip\")";
	// echo "$sql";
	$res = mysql_query($sql, $conn);
        if (!$res) {
            // echo "FAILED: " . $sql . "\n";
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql . "\n";
            echo $message;
        }
        else {
            echo "OK: " . $sql . "\n";
        }

	$sql = "INSERT INTO `log_data_2562` (`source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`) VALUES ($station, $pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\")";
	$res = mysql_query($sql, $conn);
        if (!$res) {
            // echo "FAILED: " . $sql . "\n";
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql . "\n";
            echo $message;
        }
        else {
            echo "OK: " . $sql . "\n";
        }
	mysql_close($conn);

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