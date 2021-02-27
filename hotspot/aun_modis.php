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

if (isset($_POST["period"])) {
	$period = trim($_POST["period"]);
}

if (isset($_POST["province"])) {
	$province = trim($_POST["province"]);
}

if (isset($_POST["country"])) {
	$country = trim($_POST["country"]);
}

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
      html { height: 100% }
      body { height: 100%; /*background-color:#CCC*/ }
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
			<div class="row">
				<div id="map-outer" class="col-md-12">
					<div class="col-md-2 filter">
						<div style="margin: 0 -15px;background-color: #326735;color: #fff;">
							<p style="padding: 5px;text-align:center">จุดความร้อน (ระบบ MODIS)</p>
						</div>
						<form id="realtime_modis_iframe" name="realtime_modis_iframe" method="POST" action="realtime_modis_iframe.php">
						<input type="radio" id="period" name="period" value="24" <?php if ($period == "24") { echo "checked"; } ?>>Period 24 hours<br/>
						<input type="radio" id="period" name="period" value="48" <?php if ($period == "48") { echo "checked"; } ?>>Period 48 hours<br/>
						<input type="radio" id="period" name="period" value="168" <?php if ($period == "168") { echo "checked"; } ?>>Period 1 Week<br/>
						<br/>
					<select id="province" name="province">
					<?php
						if ($province != "") {
					?>
						  <option value="<?php echo $province; ?>" selected><?php echo $province; ?> </option>					
						  <option value="">----- เลือกจังหวัด -----</option>
					<?php
						} else {
					?>
						  <option value="" selected>----- เลือกจังหวัด -----</option>					
					<?php	
						}
					?>
						  <option value="เชียงใหม่">เชียงใหม่ </option>
						  <option value="เชียงราย">เชียงราย </option>
						  <option value="น่าน">น่าน </option>
						  <option value="ลำปาง">ลำปาง </option>
						  <option value="ลำพูน">ลำพูน </option>
						  <option value="แม่ฮ่องสอน">แม่ฮ่องสอน </option>
						  <option value="พะเยา">พะเยา </option>
						  <option value="แพร่">แพร่ </option>
						  <option value="อุตรดิตถ์">อุตรดิตถ์ </option>
						  <option value="ตาก">ตาก </option>							  			  
						  <option value="พิษณุโลก">พิษณุโลก </option>
						  <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
						  <option value="กระบี่">กระบี่ </option>
						  <option value="กาญจนบุรี">กาญจนบุรี </option>
						  <option value="กาฬสินธุ์">กาฬสินธุ์ </option>
						  <option value="กำแพงเพชร">กำแพงเพชร </option>
						  <option value="ขอนแก่น">ขอนแก่น</option>
						  <option value="จันทบุรี">จันทบุรี</option>
						  <option value="ฉะเชิงเทรา">ฉะเชิงเทรา </option>
						  <option value="ชัยนาท">ชัยนาท </option>
						  <option value="ชัยภูมิ">ชัยภูมิ </option>
						  <option value="ชุมพร">ชุมพร </option>
						  <option value="ชลบุรี">ชลบุรี </option>
						  <option value="ตรัง">ตรัง </option>
						  <option value="ตราด">ตราด </option>
						  <option value="นครนายก">นครนายก </option>
						  <option value="นครปฐม">นครปฐม </option>
						  <option value="นครพนม">นครพนม </option>
						  <option value="นครราชสีมา">นครราชสีมา </option>
						  <option value="นครศรีธรรมราช">นครศรีธรรมราช </option>
						  <option value="นครสวรรค์">นครสวรรค์ </option>
						  <option value="นราธิวาส">นราธิวาส </option>
						  <option value="นนทบุรี">นนทบุรี </option>
						  <option value="บึงกาฬ">บึงกาฬ</option>
						  <option value="บุรีรัมย์">บุรีรัมย์</option>
						  <option value="ประจวบคีรีขันธ์">ประจวบคีรีขันธ์ </option>
						  <option value="ปทุมธานี">ปทุมธานี </option>
						  <option value="ปราจีนบุรี">ปราจีนบุรี </option>
						  <option value="ปัตตานี">ปัตตานี </option>
						  <option value="พระนครศรีอยุธยา">พระนครศรีอยุธยา </option>
						  <option value="พังงา">พังงา </option>
						  <option value="พิจิตร">พิจิตร </option>
						  <option value="เพชรบุรี">เพชรบุรี </option>
						  <option value="เพชรบูรณ์">เพชรบูรณ์ </option>
						  <option value="พัทลุง">พัทลุง </option>
						  <option value="ภูเก็ต">ภูเก็ต </option>
						  <option value="มหาสารคาม">มหาสารคาม </option>
						  <option value="มุกดาหาร">มุกดาหาร </option>
						  <option value="ยโสธร">ยโสธร </option>
						  <option value="ยะลา">ยะลา </option>
						  <option value="ร้อยเอ็ด">ร้อยเอ็ด </option>
						  <option value="ระนอง">ระนอง </option>
						  <option value="ระยอง">ระยอง </option>
						  <option value="ราชบุรี">ราชบุรี</option>
						  <option value="ลพบุรี">ลพบุรี </option>
						  <option value="เลย">เลย </option>
						  <option value="ศรีสะเกษ">ศรีสะเกษ</option>
						  <option value="สกลนคร">สกลนคร</option>
						  <option value="สงขลา">สงขลา </option>
						  <option value="สมุทรสาคร">สมุทรสาคร </option>
						  <option value="สมุทรปราการ">สมุทรปราการ </option>
						  <option value="สมุทรสงคราม">สมุทรสงคราม </option>
						  <option value="สระแก้ว">สระแก้ว </option>
						  <option value="สระบุรี">สระบุรี </option>
						  <option value="สิงห์บุรี">สิงห์บุรี </option>
						  <option value="สุโขทัย">สุโขทัย </option>
						  <option value="สุพรรณบุรี">สุพรรณบุรี </option>
						  <option value="สุราษฎร์ธานี">สุราษฎร์ธานี </option>
						  <option value="สุรินทร์">สุรินทร์ </option>
						  <option value="สตูล">สตูล </option>
						  <option value="หนองคาย">หนองคาย </option>
						  <option value="หนองบัวลำภู">หนองบัวลำภู </option>
						  <option value="อำนาจเจริญ">อำนาจเจริญ </option>
						  <option value="อุดรธานี">อุดรธานี </option>
						  <option value="อุทัยธานี">อุทัยธานี </option>
						  <option value="อุบลราชธานี">อุบลราชธานี</option>
						  <option value="อ่างทอง">อ่างทอง </option>
					</select>        			
						<br/>
						<input type="radio" id="country" name="country" value="0">ดู South East Asia (อาจทำให้เครื่องช้าบางครั้ง หาก hotpost มีจำนวนมาก)
						<br/>
						<input type="submit" class="btn-success" value="Query Modis"><br/><br/>
						</form>
	<!--				<select name="region">
						<option value="1	">อำเภอเมืองเชียงใหม่</option>
						<option value="2">อำเภอจอมทอง</option>
						<option value="3">อำเภอแม่แจ่ม</option>
						<option value="4">อำเภอเชียงดาว</option>
						<option value="5">อำเภอดอยสะเก็ด</option>
						<option value="6">อำเภอแม่แตง</option>
						<option value="7">อำเภอแม่ริม</option>
						<option value="8">อำเภอสะเมิง</option>
						<option value="9">อำเภอฝาง</option>
						<option value="10">อำเภอแม่อาย</option>
						<option value="11">อำเภอพร้าว</option>
						<option value="12">อำเภอสันป่าตอง</option>
						<option value="13">อำเภอสันกำแพง</option>
						<option value="14">อำเภอสันทราย</option>
						<option value="15">อำเภอหางดง</option>
						<option value="16">อำเภอฮอด</option>
						<option value="17">อำเภอดอยเต่า</option>
						<option value="18">อำเภออมก๋อย</option>
						<option value="19">อำเภอสารภี</option>
						<option value="20">อำเภอเวียงแหง</option>
						<option value="21">อำเภอไชยปราการ</option>
						<option value="22">อำเภอแม่วาง</option>
						<option value="23">อำเภอแม่ออน</option>
						<option value="24">อำเภอดอยหล่อ</option>
						<option value="25">อำเภอกัลยาณิวัฒนา</option>
					</select> -->
					</div>
					<div id="map" class="col-md-10">
					</div>
				</div>
			</div>
        </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  </body>
</html>