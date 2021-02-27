<?php 
	include "connect.php";
	
	date_default_timezone_set("Europe/London");
	
	echo $_GET['callback'];
	
	if($_GET["filename"]=="pm10" && !empty($_GET["local"])){
			
			echo "(";
				
				$sql2 = "SELECT count(log_id) as total_row FROM log_data_2561 WHERE source_id = ".mysqli_real_escape_string($mysqli, $_GET["local"]);
				
				$q2=$mysqli->query($sql2);
				$rows = $q2->fetch_assoc();
				$num = $rows['total_row'];
				
				if($num > 2000){
					$stat = $num - 500;
					$sql="SELECT log_datetime,log_pm10 FROM log_data_2561 WHERE source_id = ".mysqli_real_escape_string($mysqli, $_GET["local"])." ORDER BY log_data_2561.log_datetime ASC LIMIT {$stat},500";
				}else{
					$sql="SELECT log_datetime,log_pm10 FROM log_data_2561 WHERE source_id = ".mysqli_real_escape_string($mysqli, $_GET["local"])." ORDER BY log_data_2561.log_datetime ASC";
				}
				$q=$mysqli->query($sql);
				$data = array();
				while($rs=$q->fetch_assoc())
				{
					$day_array = array( (strtotime($rs["log_datetime"])+3600) *1000, $rs["log_pm10"]);
					array_push($data,$day_array);
				}
				echo json_encode($data, JSON_NUMERIC_CHECK);
					
			echo ");";
	}else if($_GET["filename"]=="pm2.5" && !empty($_GET["local"])){
			
			echo "(";
				$sql2 = "SELECT count(log_id) as total_row FROM log_data_2561 WHERE source_id = ".mysqli_real_escape_string($mysqli, $_GET["local"]);
				
				$q2=$mysqli->query($sql2);
				$rows = $q2->fetch_assoc();
				$num = $rows['total_row'];
				
				if($num > 2000){
					$stat = $num - 500;
					$sql="SELECT log_datetime,log_pm25 FROM log_data_2561 WHERE source_id = ".mysqli_real_escape_string($mysqli, $_GET["local"])." ORDER BY log_data_2561.log_datetime ASC LIMIT {$stat},500";
				}else{
					$sql="SELECT log_datetime,log_pm25 FROM log_data_2561 WHERE source_id = ".mysqli_real_escape_string($mysqli, $_GET["local"])." ORDER BY log_data_2561.log_datetime ASC";
				}
				$q=$mysqli->query($sql);
				$data = array();
				while($rs=$q->fetch_assoc())
				{
					$day_array = array( (strtotime($rs["log_datetime"])+3600) *1000, $rs["log_pm25"]);
					array_push($data,$day_array);
				}
				echo json_encode($data, JSON_NUMERIC_CHECK);
					
			echo ");";
	}
?>