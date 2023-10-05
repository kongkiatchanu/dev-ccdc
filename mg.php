<?php 
error_reporting(-1);

// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
$mysqli = new mysqli("localhost","dev","liveboxit","dev");

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$sql ="SELECT max(log_id) as id FROM `log_data_2562`";
$result = $mysqli->query($sql);
$rs = $result -> fetch_assoc();


$sql = "SELECT * FROM `log_data_2561` WHERE `log_id` > ".$rs['id']." AND `log_id` <= 24625804 ORDER BY `log_data_2561`.`log_id` ASC limit 500";
echo $sql;
echo '<br>';
$resultx = $mysqli->query($sql);
while($row = $resultx -> fetch_assoc()){
    $log_id = $row['log_id'];
    $station = $row['source_id'];
    $pm10 = $row['log_pm10'];
    $pm2_5 = $row['log_pm25'];
    $temp = $row['temp']!=null ? $row['temp'] : 0;
    $humid = $row['humid']!=null ? $row['humid'] : 0;
    $log_datetime = $row['log_datetime'];
    $src_ip = $row['source_ip'];
   
    $sql2 = "INSERT INTO `log_data_2562` (`log_id`, `source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, log_datetime, `source_ip`) VALUES ($log_id, $station, $pm10, $pm2_5, $temp, $humid, \"$log_datetime\" , \"$src_ip\")";
    $res2 = $mysqli->query($sql2);
    echo $sql2;
    echo '<br/>';
    echo $log_id.'-'.$station.'<hr/>';
    header("Refresh:10");
}