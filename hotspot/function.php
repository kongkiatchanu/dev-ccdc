<?php

$conn = mysql_connect("localhost", "root", "lbcmsuccess");
mysql_query("SET NAMES utf8", $conn);
mysql_select_db("hotspot", $conn);

function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

//18.7151741,98.9786613
function distance_cnx($lat1, $lon1) {

  $lat2 = 18.7151741;
  $lon2 = 98.9786613;
  $unit = "K";

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

function is_province($lat, $lon, $province_name) {
	$flag = false;
	$json_content = trim(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lon."&sensor=true&key=AIzaSyDR8HOgNdg-TmLRquiQxHZ8Pa4XEhJZdJ0"));
	$array_geocode = (array)json_decode($json_content);
	$array_results = (array)($array_geocode[results][0]);
	$formatted_address = $array_results[formatted_address];
	//echo $formatted_address;
	if (ereg($province_name, $formatted_address)) {
		$flag = true;
	}
	return $flag;
}

function ConvertToThaiDate  ($date,$short) {
		if($date){
			if($short){
				$MONTH = array("", "ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
			}else{
				$MONTH = array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
			}
			$dt = explode("-", $date);
			$tyear = $dt[0];
			$dt[0] = $dt[2] +0;
			$dt[1] = $MONTH[$dt[1]+0];
			$dt[2] = $tyear+543;
			return join(" ", $dt);
		}else{
			return "<font color=\"#FF0000\">ไม่ระบุ</font>";
		}
}

function ConvertRToThaiDate($date1,$date2){
	$MONTH = array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");	
	$dt = explode("-", $date1);
	$dt2 = explode("-", $date2);	
	$tyear = $dt[0]+543;
	
	$dt[0] = $dt[2] +0;
	$dt[1] = $MONTH[$dt[1]+0];
	$dt2[0] = $dt2[2] +0;
	$dt2[1] = $MONTH[$dt2[1]+0];
	
	return $dt[0].' '.$dt[1].' - '.$dt2[0].' '.$dt2[1].' '.$tyear;
}
function getMonth($date,$short) {
		if($date){
			if($short){
				$MONTH = array("", "ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
			}else{
				$MONTH = array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
			}
			$dt = explode("-", $date);
			$tyear = $dt[0];
			$dt[2] = $tyear+543;
			return $MONTH[$dt[1]+0].' '.$dt[2];
			
		}else{
			return "<font color=\"#FF0000\">ไม่ระบุ</font>";
		}
	}
?>