<?php
 $pm10 = $_GET['pm10'];
 $pm2_5 = $_GET['pm2_5'];
 $secret = $_GET['secret'];
 $station = $_GET['id'];
 $temp = $_GET['temp'];
 $humid = $_GET['humid'];
 $ts = $_GET['timestamp'];
 $dt = $_GET['dt'];
 $webid = $_GET['webid'];
 $nickname = $_GET['nickname'];
 $src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
 
 echo '<pre>';
 print_r($_GET);
 echo '</pre>';

if (!$temp) $temp = "NULL"  ;
if (!$humid) $humid = "NULL";
    
if ($secret != 'e96cfe7eb8b48d6b5c492dklsd553bc743eccde62c6efce7aacba1e9') {
  die('access denied. '. print_r($_GET, 1));
} 

$conn = mysql_connect("localhost", "dev", "liveboxit");
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}

if($pm2_5=="" || $pm2_5==0) {
    echo "no pm2_5";
	exit;
}
else {

    echo "ccdc be insterted.";
	mysql_select_db("dev", $conn);
	$sql = "INSERT INTO `log_tic_2562` (`log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`, `log_datetime`, `source_id`) VALUES ($pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\", \"$ts\", $webid)";
	$res = mysql_query($sql, $conn);
	echo "insert result = " . print_r($res, 1) . "\r\n";
	echo '<br>';
	echo "sql=".print_r($sql, 1);
	mysql_close($conn);
	    
    echo "$POST=" . print_r(json_encode($_GET), 1);
}

?>
