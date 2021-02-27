<?php 
include "connect.php";
/*
                                class="explain-text-box good" ////// charactor-01.png ////// good
                                class="explain-text-box moderate" ////// charactor-02.png ////// moderate
                                class="explain-text-box unhealthy" ////// charactor-03.png  ////// unhealthy
                                class="explain-text-box very-unhealthy" ////// charactor-04.png ////// very-unhealthy
                                class="explain-text-box hazardous" ////// charactor-05.png  ////// hazardous
                            */
$messages = array();

function report_type($val){
	if($val<=50){
		$txt = 'good';
	}else if($val>50 && $val<=100){
		$txt = 'moderate';
	}else if($val>100 && $val<=200){
		$txt = 'unhealthy';
	}else if($val>200 && $val<=300){
		$txt = 'very-unhealthy';
	}else if($val>300 && $val<=600){
		$txt = 'hazardous';
	}else{
		$txt = 'hazardous';
	}
	return $txt;
}

function calAQIPM10($val){
	if($val<=40){
		$data = (((50-0)*($val-0))/(40-0))+0;
	}else if($val>40 && $val<=120){
		$data=(((100-50)*($val-40))/(120-40))+50;
	}else if($val>120 && $val<=350){
		$data=(((200-100)*($val-120))/(350-120))+100;
	}else if($val>350 && $val<=420){
		$data=(((300-200)*($val-350))/(420-350))+200;
	}else if($val>420 && $val<=600){
		$data=(((500-300)*($val-420))/(600-420))+300;
	}else{
		$data=500;
	}
	return number_format($data);
}

function calAQIPM10txt($val){
	if($val<=50){
		$txt = 'ดี';
	}else if($val>50 && $val<=100){
		$txt = 'ปานกลาง';
	}else if($val>100 && $val<=200){
		$txt = 'มีผลกระทบต่อสุขภาพ';
	}else if($val>200 && $val<=300){
		$txt = 'มีผลกระทบต่อสุขภาพมาก';
	}else if($val>300 && $val<=600){
		$txt = 'อันตราย';
	}else{
		$txt = 'อันตราย';
	}
	return $txt;
}

function calAQIPM10Img($val){
	if($val<=50){
		$img = 1;
	}else if($val>50 && $val<=100){
		$img = 2;
	}else if($val>100 && $val<=200){
		$img = 3;
	}else if($val>200 && $val<=300){
		$img = 4;
	}else if($val>300 && $val<=600){
		$img = 5;
	}else{
		$img = 5;
	}
	return $img;
}

function calAQIPM25($val){
	if($val<=25){
		$data = (((50-0)*($val-0))/(25-0))+0;
		}else if($val>25 && $val<=50){
		$data = (((100-50)*($val-25))/(50-25))+50;
	}else if($val>50 && $val<=150){
		$data = (((200-100)*($val-50))/(150-50))+100;
	}else if($val>150 && $val<=250){
		$data=(((300-200)*($val-150))/(200-150))+200;
	}else if($val>250 && $val<=500){
		$data=(((500-300)*($val-250))/(500-250))+300;
	}else{
		$data=500;
	}
	return number_format($data);
}


if(!empty($_GET['s'])){
	$s = mysqli_real_escape_string($mysqli,$_GET['s']);
	$sql="SELECT log_data_2561.log_pm10,log_data_2561.log_pm25,log_data_2561.log_wind,source.location_name,DATE_FORMAT(log_data_2561.log_datetime, '%d %M %Y , %H:%i') as thaidate FROM log_data_2561 
	left join source on log_data_2561.source_id = source.source_id
	WHERE log_data_2561.source_id ={$s} ORDER BY log_data_2561.log_datetime DESC LIMIT 1";

	$q=$mysqli->query($sql);
	$rs=$q->fetch_assoc();
	if($_GET['type']=="pm10"){
		$messages["value"] =calAQIPM10($rs["log_pm10"]);
		$messages["txt"] = calAQIPM10txt(calAQIPM10($rs["log_pm10"]));
		$messages["name"] = $rs["location_name"];
		$messages["date"] = $rs["thaidate"];
		$messages["img"] = '<img style="margin:0 auto;width:100%;" src="/template/img/Air%20quality-0'.calAQIPM10Img(calAQIPM10($rs["log_pm10"])).'.png">';
		$messages["wind"] = $rs['log_wind'];
		$messages["report"] = 'ฝุ่นละอองขนาดเล็กว่า 10 ไมครอน (PM10) เท่ากับ '.$rs["log_pm10"].' ไมโครกรัมต่อลูกบาศก์เมตร และมีค่า AQI เท่ากับ '.calAQIPM10($rs["log_pm10"]).' ถือว่าคุณภาพอากาศอยู่ในเกณฑ์ '.calAQIPM10txt(calAQIPM10($rs["log_pm10"]));
		$messages["report_type"] = report_type(calAQIPM10($rs["log_pm10"]));
	}else if($_GET['type']=="pm25"){
		$messages["value"] =calAQIPM25($rs["log_pm25"]);
		$messages["txt"] = calAQIPM10txt(calAQIPM25($rs["log_pm25"]));
		$messages["name"] = $rs["location_name"];
		$messages["date"] = $rs["thaidate"];
		$messages["img"] = '<img style="margin:0 auto;width:100%;" src="/template/img/Air%20quality-0'.calAQIPM10Img(calAQIPM25($rs["log_pm25"])).'.png">';
		$messages["wind"] = $rs['log_wind'];
		$messages["report"] = 'ค่าเฉลี่ยรายชั่วโมงของฝุ่นที่มีขนาดเล็กกว่า 2.5 ไมครอน (PM2.5) เท่ากับ '.$rs["log_pm25"].' ไมโครกรัมต่อลูกบาศก์เมตร และมีค่า AQI เท่ากับ '.calAQIPM25($rs["log_pm25"]).' ถือว่าคุณภาพอากาศอยู่ในเกณฑ์เกณฑ์ '.calAQIPM10txt(calAQIPM25($rs["log_pm25"]));
		$messages["report_type"] = report_type(calAQIPM25($rs["log_pm25"]));
	}
	
	echo json_encode($messages);
}
	

	

?>