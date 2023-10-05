<?php
    include_once "post_params.php";
    
    
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $src_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    file_put_contents('/home/dev/public_html/assets/api/logs/demo_mini_array.log', date("Y-m-d H:i:s") . "|" . $src_ip . "|" . var_export($_POST, true) . "\n", FILE_APPEND);

    $x = json_decode($_POST['data_array']);

    if (!$x) {
        print_r($_POST['data_array']);
        die("no data_array\n");
    }

    $mysqli = new mysqli("localhost","dev","liveboxit","dev");

    // Check connection
    if ($mysqli -> connect_errno) {
      echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
      exit();
    }
    foreach ($x as $idx => $sensor) {
        $pm1 = $sensor->pm1;
        $pm10 = $sensor->pm10;
        $pm2_5 = $sensor->pm2_5;
        $station = $sensor->id;
        $temp = $sensor->temp;
        $humid = $sensor->humid;
        $nickname = $sensor->nickname;

        if (!$pm1) $pm1 = "NULL";
        if (!$pm10) $pm10 = "NULL";
        if (!$pm2_5) $pm2_5 = "NULL";
        if (!$temp) $temp = "NULL";
        if (!$humid) $humid = "NULL";
        if (!$nickname) $nickname = "NULL";

        $sql2 = "INSERT INTO `log_zdata` (`source_id`, `log_pm1`, `log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`) VALUES ($station, $pm1, $pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\");";
        $sql = "INSERT INTO `log_mini_2561` (`source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`) VALUES ($station, $pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\");";
       
        if ($pm10 == "NULL" || $pm2_5 == "NULL") {
            echo "SKIP: " . $sql . "\n";
            continue;
        }
        $res2 = $mysqli->query($sql2);
        $res = $mysqli->query($sql);


        if (!$res) {
            // echo "FAILED: " . $sql . "\n";
            //$message  = 'Invalid query: ' . mysql_error() . "\n";
            //$message .= 'Whole query: ' . $sql . "\n";
           // echo $message;
        }
        else {
            echo "OK: " . $sql . "\n";
        }
		
		
    }
?>