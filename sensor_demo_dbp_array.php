<?php
    include_once "post_params.php";
    // print_r($_POST);
    // echo "->";
    // echo $_POST['data_array'];
    // echo "<-";

    $x = json_decode($_POST['data_array']);

    if (!$x) {
        print_r($_POST['data_array']);
        die("no data_array\n");
    }

    $conn = mysql_connect("localhost", "dev", "liveboxit");
    if (!$conn) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("dev", $conn);
    foreach ($x as $idx => $sensor) {
        $pm10 = $sensor->pm10;
        $pm2_5 = $sensor->pm2_5;
        $station = $sensor->id;
        $temp = $sensor->temp;
        $humid = $sensor->humid;
        $nickname = $sensor->nickname;

        if (!$pm10) $pm10 = "NULL";
        if (!$pm2_5) $pm2_5 = "NULL";
        if (!$temp) $temp = "NULL";
        if (!$humid) $humid = "NULL";
        if (!$nickname) $nickname = "NULL";

        $sql = "INSERT INTO `log_data_2562` (`source_id`, `log_pm10`, `log_pm25`, `temp`, `humid`, `nickname`, `source_ip`) VALUES ($station, $pm10, $pm2_5, $temp, $humid, \"$nickname\", \"$src_ip\")";

        $res = mysql_query($sql, $conn);

        if ($pm10 == "NULL" || $pm2_5 == "NULL") {
            echo "SKIP: " . $sql . "\n";
            continue;
        }

        if (!$res) {
            // echo "FAILED: " . $sql . "\n";
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql . "\n";
            echo $message;
        }
        else {
            echo "OK: " . $sql . "\n";
        }
    }

    mysql_close($conn)
?>