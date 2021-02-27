<?php
require_once("function.php");
require_once("./lib/gpointconverter.class.php");
$total_hotspot = 0;
$last_date = "";
$last_time = "";
$last_datetime = "";

$satellite_name = array("T" => "Terra", "A" => "Aqua");

//init value
$period = "24";
$province = "เชียงใหม่";
$country = "1";

if (isset($_GET["period"])) {
	$period = trim($_POST["period"]);
}

if (isset($_GET["province"])) {
	$province = trim($_GET["province"]);
}

if (isset($_GET["amphur"])) {
	$amphur = trim($_GET["amphur"]);
}

if (isset($_GET["country"])) {
	$country = trim($_GET["country"]);
}

$sql="SELECT * FROM `province` WHERE `PROVINCE_NAME` LIKE '%เชียงใหม่%' ORDER BY `PROVINCE_NAME` ASC";
$result = mysql_query($sql, $conn);
$row = mysql_fetch_array($result);
$set_province_id = $row["PROVINCE_ID"];



$gpoint = new GpointConverter();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>CMU CCDC MODIS MAP</title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
		@import "/css/font.css";
	  html { height: 100% }
      body { height: 100%; font-family: "Kanit-Light";/*background-color:#CCC*/ }
      #map-outer {height: 650px;  padding:20px 0; background-color:#FFF }
      #map-outer .filter{margin-bottom:20px;background-color: #9DC13B;}
	  #map { height: 600px }
	  @media all and (max-width: 991px) {
		#map-outer  { height: 650px }
		}    
		
	</style>
  </head>
  <body>
    <script>
    
      // The following example creates complex markers to indicate beaches near
      // Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
      // to the base of the flagpole.

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 8,
          center: {lat: 18.783426, lng: 98.998510}
        });

        setMarkers(map);
      }

      // Data for the markers consisting of a name, a LatLng and a zIndex for the
      // order in which these markers should display on top of each other.
/*      var beaches = [
        ['Bondi Beach', -33.890542, 151.274856, 4],
        ['Coogee Beach', -33.923036, 151.259052, 5],
        ['Cronulla Beach', -34.028249, 151.157507, 3],
        ['Manly Beach', -33.80010128657071, 151.28747820854187, 2],
        ['Maroubra Beach', -33.950198, 151.259302, 1]
      ];*/
      
      var beaches = [
<?php
	$zindex = 1;
//	$sql = "SELECT *,ADDTIME(CAST(acq_time AS TIME), '07:00:00') AS new_acq_time FROM modis WHERE latitude LIKE '18.%'";
/*	$sql = "
		SELECT *,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime
		FROM modis
		WHERE acq_date='2016-04-06'
		ORDER by acq_datetime
	";*/

	$sql = "
		SELECT modis.*,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime, modis_meta.raw_json
		FROM modis
        LEFT JOIN modis_meta
        ON modis.viirs_id=modis_meta.modis_id
		WHERE (((CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR)) BETWEEN (NOW() - INTERVAL ".$period." HOUR) AND NOW())
		AND raw_json LIKE '%".$province."%' 
		AND raw_json LIKE '%ประเทศไทย%' 
		ORDER by acq_datetime	

	";
	
	if ($country == "0") {
		$sql = "
			SELECT modis.*,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime, modis_meta.raw_json
			FROM modis
			LEFT JOIN modis_meta
			ON modis.viirs_id=modis_meta.modis_id
			WHERE (((CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR)) BETWEEN (NOW() - INTERVAL ".$period." HOUR) AND NOW())
			ORDER by acq_datetime	

		";	
		
	}

	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_array($result)) {
	
	/* latitude, longitude, brightness, scan, track, acq_date, acq_time, satellite,bright_t31, frp */

		$latitude = $row["latitude"];
		$longitude = $row["longitude"];
		$utm_result = $gpoint->convertLatLngToUtm($latitude, $longitude);
		$UTMEasting = $utm_result[0];
		$UTMNorthing = $utm_result[1];
		$UTMZone = $utm_result[2];
		$acq_datetime = $row["acq_datetime"];
		$satellite = $satellite_name[trim($row["satellite"])];
		$bright_t31 = $row["bright_t31"];
		$frp = $row["frp"];
		$raw_json = trim($row["raw_json"]);
		$gg = (array)json_decode($raw_json);	
		$results = (array)$gg['results'][0];	
		$formatted_address = $results['formatted_address'];
//		if (distance_cnx($latitude, $longitude) <= 100) {
//		if (is_province($latitude, $longitude, "Chiang Mai")) {
			echo "['hotspot$zindex', $latitude, $longitude, $zindex, '$acq_datetime', '$satellite', '$bright_t31', '$frp', '$UTMEasting', '$UTMNorthing', '$UTMZone', '$formatted_address'],\n";
			//$last_date = $row["acq_date"];
			//$last_time = $row["new_acq_time"];
			$last_datetime = $row["acq_datetime"];
			$total_hotspot++;
//		}}
		$zindex++;
	}
?>
      ];     

      function setMarkers(map) {
        // Adds markers to the map.

        // Marker sizes are expressed as a Size of X,Y where the origin of the image
        // (0,0) is located in the top left of the image.

        // Origins, anchor positions and coordinates of the marker increase in the X
        // direction to the right and in the Y direction down.
        var image = {
          url: '/api/hotspot/images/fire.gif',
          // This marker is 20 pixels wide by 32 pixels high.
          size: new google.maps.Size(15, 15),
          // The origin for this image is (0, 0).
          origin: new google.maps.Point(0, 0),
          // The anchor for this image is the base of the flagpole at (0, 32).
          anchor: new google.maps.Point(0, 0)
        };
        // Shapes define the clickable region of the icon. The type defines an HTML
        // <area> element 'poly' which traces out a polygon as a series of X,Y points.
        // The final coordinate closes the poly by connecting to the first coordinate.
        var shape = {
          coords: [1, 1, 1, 20, 18, 20, 18, 1],
          type: 'poly'
        };
        for (var i = 0; i < beaches.length; i++) {
          var beach = beaches[i];
          var marker = new google.maps.Marker({
            position: {lat: beach[1], lng: beach[2]},
            map: map,
            icon: image,
            shape: shape,
            title: beach[0],
            zIndex: beach[3],
            optimized: false,
          });
          //var contentString = null;
//          var contentString = 'ละติจูด: ' + beach[1] + '<br/>ลองจิจูด: ' + beach[2] + '<br/>เวลา: ' + beach[4] + '<br/>ดาวเทียม: ' + beach[5] + '<br/>Brightness (kelvin): ' + beach[6] + '<br/>frp: ' + beach[7];
			var contentString = beach[4] + '<br/>' + beach[11] + '<br/>พิกัด_X ' + beach[8] + '<br/>พิกัด_Y ' + beach[9] + '<br/>Satellite ' + beach[5];
          var infowindow = null;
			infowindow = new google.maps.InfoWindow({
			content: 'content holder'
			});
/*		  
		  google.maps.event.addListener(marker, 'click', function () {
				infowindow.setContent(contentString);
				infowindow.open(map, this);
			});*/
			
google.maps.event.addListener(marker,'click', (function(marker,contentString,infowindow){ 
        return function() {
           infowindow.setContent(contentString);
           infowindow.open(map,marker);
        };
    })(marker,contentString,infowindow));			

		  
// 			marker.addListener('click', function() {
// 				infowindow.open(map, marker);
// 			  });
        }
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAg79jeP4V_QoMuchIFak923yM32gfOCHU&callback=initMap">
    </script>
    <div class="container">
		<h4>MODIS Near Real Time : Total <?php echo $total_hotspot; ?> hotspot(s).</h4>
		<h4>Date : <?=date('d/m/Y')?></h4>
		<div class="row">
			<div id="map-outer" class="col-md-12">
				<div id="map" class="col-md-12"></div>
				<div id="filter" style="position: absolute;top: 30px;right:10px;">

					<div class="panel-group" id="accordion">
							<div class="panel panel-default">
								<div class="panel-heading">
								  <h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" style="text-decoration: none;">
									  ตัวกรองจุดความร้อน&nbsp;&nbsp;&nbsp;&nbsp;
									</a><i class="indicator glyphicon glyphicon-chevron-down pull-right"></i>
								  </h4>
								</div>
								<div id="collapseOne" class="panel-collapse collapse in">
									<div class="panel-body">
										<form class="form-horizontal" id="realtime_modis_iframe" name="realtime_modis_iframe" method="GET" action="">
											<div class="form-group">
												<div class="col-md-12">
													<div class="radio">
														<label><input type="radio" id="country" name="country" value="0" <?php if ($country == 0) { echo "checked"; } ?>>ดู South East Asia<br/>(อาจทำให้เครื่องช้าบางครั้ง หาก hotpost มีจำนวนมาก)</label>
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-md-12">
													<div class="radio">
														<label><input type="radio" id="period" name="period" value="24" <?php if ($period == "24") { echo "checked"; } ?>>Period 24 hours</label>
													</div>
													<div class="radio">
														<label><input type="radio" id="period" name="period" value="48" <?php if ($period == "48") { echo "checked"; } ?>>Period 48 hours</label>
													</div>
													<div class="radio">
														<label><input type="radio" id="period" name="period" value="168" <?php if ($period == "168") { echo "checked"; } ?>>Period 1 Week</label>
													</div>
													<div class="radio">
														<label><input type="radio" id="period" name="period" value="720" <?php if ($period == "720") { echo "checked"; } ?>>Period 1 Month</label>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-12">
													<select class="form-control" id="province" name="province">
														<?php 
															
															$sql="SELECT * FROM province ORDER BY province.PROVINCE_NAME ASC";
															$result = mysql_query($sql, $conn);
															while ($row = mysql_fetch_array($result)) {
														?>
															<option value="<?=trim($row["PROVINCE_NAME"])?>" <?=trim($row["PROVINCE_NAME"])==$province?'selected':''?>><?=trim($row["PROVINCE_NAME"])?></option>
														
														<?php
															
															}
														?>
													</select>        	
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-12">
													<select class="form-control" id="amphur" name="amphur">
														<option value=""> - เลือกอำเภอทั้งหมด - </option>
														<?php 
															if($set_province_id!=null){
																$sql="SELECT * FROM amphur WHERE PROVINCE_ID = ".$set_province_id." ORDER BY amphur.AMPHUR_NAME ASC";
																$result = mysql_query($sql, $conn);
																while ($row = mysql_fetch_array($result)) {
														?>
																	<option value="<?=trim($row["AMPHUR_NAME"])?>" <?=trim($row["AMPHUR_NAME"])==$amphur?'selected':''?>><?=trim($row["AMPHUR_NAME"])?></option>
														<?php
																}
															}
															
														?>
														
													</select>        	
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-md-12 text-center">
													<button type="submit" class="btn-success">Query Modis</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
					</div>

				</div>
			</div>
		</div>
		
		<?php if($country!=0){?>
		<!-------------------------------------------------------------->
		<hr style="margin:30px 0;background: #f9f9f9;">
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12">
				
				<?php 
					$sql = "
						SELECT modis.*,(CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR) AS acq_datetime, modis_meta.raw_json
						FROM modis
						LEFT JOIN modis_meta
						ON modis.viirs_id=modis_meta.modis_id
						WHERE (((CAST(CONCAT(acq_date,' ',acq_time) AS DATETIME)  + INTERVAL 7 HOUR)) BETWEEN (NOW() - INTERVAL ".$period." HOUR) AND NOW())
						AND raw_json LIKE '%".$province."%' 
						AND raw_json LIKE '%".$amphur."%' 
						AND raw_json LIKE '%ประเทศไทย%' 
						ORDER by acq_datetime";
					$result = mysql_query($sql, $conn);
					if(mysql_num_rows($result)){?>
						<h3 class="text-center">รายงานสถานการณ์ไฟป่า</h3>
						<h3 class="text-center">วันที่ <?=ConvertToThaiDate(date('Y-m-d H:i:s'),0)?> (ย้อนหลัง <?=$period?> ชั่วโมง)</h3>
						<h3 class="text-center">พบจุดความร้อน(Hotspot) บริเวณพื้นที่ในจังหวัด<?php echo $province; ?> ทั้งหมด <?php echo $total_hotspot; ?> จุด</h3>
				<div class="table-responsive"> 
					<table class="table table-striped table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th class="text-center">จุดที่</th>
								<th class="text-center">ละติจูด</th>
								<th class="text-center">ลองจิจูด</th>
								<th class="text-center">X</th>
								<th class="text-center">Y</th>
								<th class="text-center">ว/ด/ป</th>
								<th class="text-center">เวลา</th>
								<th class="text-center">ตำบล</th>
								<th class="text-center">อำเภอ</th>
								<th class="text-center">จังหวัด</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$i=0;
							while ($row = mysql_fetch_array($result)) {
							$i++;
							$utm_result = $gpoint->convertLatLngToUtm($row["latitude"], $row["longitude"]);
							$tem_date = $row["acq_datetime"];
							$time = substr($tem_date,11,8);
							$raw_json = trim($row["raw_json"]);
							$gg = (array)json_decode($raw_json);	
							$results = (array)$gg['results'][0];	
							$formatted_address = $results['formatted_address'];
							/*
							$dis_a = strpos($formatted_address,'ตำบล')+12  ;
							$dis_b = strpos($formatted_address,'อำเภอ')-1;
							$dis_sum = $dis_b - $dis_a;
							$dis =  substr($formatted_address,$dis_a,$dis_sum);	
							*/
							$ad = explode(" ", $formatted_address);
							$dis_key ="";
							$am_key ="";
							foreach($ad as $key => $val){
								if($val=="ตำบล"){
									$dis_key =$key;
									$dis_key++;
								}
								if($val=="อำเภอ"){
									$am_key =$key;	
									$am_key++;
								}
								if($val=="อำเภอเมืองเชียงราย"){
									$am_key =$key;
								}
								
							}
							$pro_key = $am_key+1
							
						?>	
								<tr>
									<td class="text-center"><?=$i?></td>
									<td class="text-center"><?=$row["latitude"]?></td>
									<td class="text-center"><?=$row["longitude"]?></td>
									<td class="text-center"><?=$utm_result[0]?></td>
									<td class="text-center"><?=$utm_result[1]?></td>
									<td class="text-center"><?=ConvertToThaiDate($row["acq_datetime"],1)?></td>
									<td class="text-center"><?=$time?></td>
									<td class="text-center"><?=$ad[$dis_key]?></td>
									<td class="text-center"><?=$ad[$am_key]?></td>
									<td class="text-center"><?=$ad[$pro_key]?></td>
								</tr>
							
						<?php	
							}	?>
							
							</tbody>
					</table>
				</div>
						<?php }?>
		
						
			</div>
		</div>
		<!-------------------------------------------------------------->
		<?php } ?>
		<!-------------------------------------------------------------->

    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>ript>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script>
	function toggleChevron(e) {
		$(e.target)
			.prev('.panel-heading')
			.find("i.indicator")
			.toggleClass('glyphicon-chevron-down glyphicon-chevron-right');
		}
		$('#accordion').on('hidden.bs.collapse', toggleChevron);
		$('#accordion').on('shown.bs.collapse', toggleChevron);
		
		$('#province').on('change',function(){
			if( this.value ){
                $.get( "http://cmuccdc.org/api/hotspot/getAmphur.php?p_name="+this.value, function( data ) {
					if(data){
						$("#amphur").html(data);
					}
				});

            }   
		});
	</script>
  </body>
</html>	
