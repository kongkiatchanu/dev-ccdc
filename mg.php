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
$sql = "SELECT * FROM `log_data_2561` WHERE `log_id` BETWEEN 24313865 AND 24625804 ORDER BY `log_data_2561`.`log_id` ASC ";
$result = $mysqli->query($sql);
while($row = $result -> fetch_array()){
    echo '<pre>';
    print_r($row);
    echo '</pre>';
    exit;
}