<?php
	$src_ip 	= $_SERVER['HTTP_X_FORWARDED_FOR'];
	$data_array = $_POST['data_array'];
	$secret 	= $_POST['secret'];
    if ($secret != 'e96cfe7eb8b48d6b5c492de81383275fce7a8bc743eccde62c6efce7aacba1e9') {
        die("context: " .print_r($_POST, 1) . " access denied.");
    }

    $x = json_decode($data_array);

    if (!$x) {
        print_r($data_array);
        die("no data_array\n");
    }
	
	$mysqli = new mysqli("localhost","dev","liveboxit","dev");

    // Check connection
    if ($mysqli -> connect_errno) {
      echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
      exit();
    }
	
	foreach ($x as $idx => $sensor) {
		
		$meter_id 			= $sensor->meter_id!=null? $sensor->meter_id:'NULL';
		$sensor_voltage_V 	= $sensor->sensor_voltage_V!=null? $sensor->sensor_voltage_V:'NULL';
		$sensor_current_A 	= $sensor->sensor_current_A!=null? $sensor->sensor_current_A:'NULL';
		$sensor_power_W 	= $sensor->sensor_power_W!=null? $sensor->sensor_power_W:'NULL';
		$sensor_energy_kWh 	= $sensor->sensor_energy_kWh!=null? $sensor->sensor_energy_kWh:'NULL';
		$sensor_frequency_Hz = $sensor->sensor_frequency_Hz!=null? $sensor->sensor_frequency_Hz:'NULL';
		$sensor_extra_1 = $sensor->sensor_extra_1!=null? $sensor->sensor_extra_1:'NULL';
		$sensor_extra_2 = $sensor->sensor_extra_2!=null? $sensor->sensor_extra_2:'NULL';
		$sensor_extra_3 = $sensor->sensor_extra_3!=null? $sensor->sensor_extra_3:'NULL';
		$sensor_extra_4 = $sensor->sensor_extra_4!=null? $sensor->sensor_extra_4:'NULL';

		$sql = "INSERT INTO dev.meter_data (data_id, meter_id, sensor_voltage_V, sensor_current_A, sensor_power_W, sensor_energy_kWh, sensor_frequency_Hz, sensor_extra_1, sensor_extra_2, sensor_extra_3, sensor_extra_4, data_time) VALUES (NULL, {$meter_id}, {$sensor_voltage_V}, {$sensor_current_A}, {$sensor_power_W}, {$sensor_energy_kWh}, {$sensor_frequency_Hz}, {$sensor_extra_1}, {$sensor_extra_2}, {$sensor_extra_3}, {$sensor_extra_4}, NOW());";
		$res = $mysqli->query($sql);
		
		if (!$res) {
            // echo "FAILED: " . $sql . "\n";
          //  $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql . "\n";
            echo $message;
        }
        else {
            echo "OK: " . $sql . "\n";
        }
	}
	
?>