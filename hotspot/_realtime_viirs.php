<?php
include("function.php");
$conn = mysql_connect("localhost", "root", "lbcmsuccess");
mysql_select_db("hotspot", $conn);
$total_hotspot = 0;
$last_date = "";
$last_time = "";
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>CMU CCDC VIIRS MAP</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
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
	$sql = "SELECT *,ADDTIME(CAST(acq_time AS TIME), '07:00:00') AS new_acq_time FROM viirs WHERE latitude LIKE '18.%'";
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_array($result)) {
		$latitude = $row["latitude"];
		$longitude = $row["longitude"];
		if (distance_cnx($latitude, $longitude) <= 100) {
		if (is_province($latitude, $longitude, "Chiang Mai")) {
			echo "['hotspot$zindex', $latitude, $longitude, $zindex],\n";
			$last_date = $row["acq_date"];
			$last_time = $row["new_acq_time"];
			$total_hotspot++;
		}}
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
          anchor: new google.maps.Point(0, 32)
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
        }
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAg79jeP4V_QoMuchIFak923yM32gfOCHU&callback=initMap">
    </script>
    		<h1><font color="red">VIIRS : Total <?php echo $total_hotspot; ?> hotspot(s) found at <?php echo $last_date." ".$last_time; ?></h1>
        <div id="map"></div>
  </body>
</html>