<?php 
	require_once("function.php");
	
	if($_GET["p_name"]!=null){
		$name=mysql_real_escape_string($_GET["p_name"]);
		$sql="SELECT PROVINCE_ID FROM province WHERE PROVINCE_NAME LIKE '%".$name."%' ORDER BY PROVINCE_NAME ASC";
		$result = mysql_query($sql, $conn);
		$row = mysql_fetch_array($result);
		if($row){
			echo '<option value=""> - เลือกอำเภอทั้งหมด - </option>';
			$sql2="SELECT * FROM amphur WHERE PROVINCE_ID = ".$row["PROVINCE_ID"]." ORDER BY AMPHUR_NAME ASC";
			$result2 = mysql_query($sql2, $conn);
			while ($row2 = mysql_fetch_array($result2)) {?>
														
				<option value="<?=trim($row2["AMPHUR_NAME"])?>"><?=trim($row2["AMPHUR_NAME"])?></option>
			<?php
			}
		}
	}
	
?>