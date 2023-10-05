<?php
	include "connect.php";
	$sql="SELECT * FROM source WHERE source_id =".mysqli_real_escape_string($mysqli, $_GET['local']);
	$q=$mysqli->query($sql);
	if($q->num_rows)
	{
		$rs=$q->fetch_assoc();
	}else{
		header("Location: /");
		exit();
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Particulate Matter</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>

</head>

<style>
.tooltop{
	background-color:#1E90FF;
	color:#333;
	padding:10px;
	border: 1px solid;
    border-radius: 10px;
	padding-top:5%;		
}
.boxtool_mod{background-color:#00FF00;}
.boxtool_sen{background-color:yellow;}
.boxtool_unheal{background-color:orange;}
.boxtool_haz{background-color:red;}
#chart_container{height:295px !important;}
</style>
<body>
	<div id="chart_container" height="100"></div>
</body>
<script>
$(function () {
    var seriesOptions = [],
        seriesCounter = 0,
        names = ['PM10',"PM2.5"];

    $.each(names, function (i, name) {
		var url = 'json_data2.php?filename=' + name.toLowerCase() + '&local=<?=$_GET["local"]?>&callback=?';
        $.getJSON(url,    function (data) {
            seriesOptions[i] = {
                name: name,
                data: data		
            };
            seriesCounter += 1;

            if (seriesCounter === names.length) {
                createChart();
            }
        });
    });
	
	function createChart() {

        $('#chart_container').highcharts('StockChart', {
			rangeSelector : {
                buttons: [{
                    type: 'day',
                    count: 1,
                    text: 'day'
                },{
                    type: 'all',
                    text: 'All'
                }],
                inputEnabled: false, // it supports only days
                selected : 1 // all
            },credits: {enabled: false},
			title : {text : '@<?=$rs["location_name"]?>'},
			yAxis: [{
				title: {
                    text: 'ug/m3'
                },
                lineWidth: 1,
                opposite:false,
                labels: {
                    align: 'right',
                    x: -10
                }/*,
				plotLines: [{
                    value: 120,
                    color: 'red',
                    dashStyle: 'shortdash',
                    width: 2,
                    label: {
                        text: '120 ug/m3'
                    }
                }]*/
            }],
            tooltip: {
				useHTML:true,
				backgroundColor: "rgba(255,255,255,0)",
				borderWidth: 0,
				borderRadius: 0,
			    shadow: false,
                formatter: function() {

					var  p1 = this.points[0].y;
					var  p2 = this.points[1].y;
					var  type = "";
					
										
					if (p1 <=40){
						var  type = "boxtool_goo";

					}else if (p1 >=41 && p1<=120){
						var  type = "boxtool_mod";

		  
					}else if (p1 >=121 && p1<=350){
						var  type = "boxtool_sen";
						
					}else if (p1 >=351 && p1<=420){
						var  type = "boxtool_unheal";	
						
					}else if (p1 >=421){
						var  type = "boxtool_haz";
					}
					
					txtshow = '<div class="tooltop '+type+'">'+  
								Highcharts.dateFormat('%e %b %Y %H:%M', new Date(this.x)) +'<br />' +
								'PM10 : <b>' + Highcharts.numberFormat(p1, 3) +'</b> ug/m3<br />' +
								'PM2.5 : <b>' + Highcharts.numberFormat(p2, 3) +'</b> ug/m3<br />' +
							'</div>';

					return txtshow;
				}
				
            },
            series: seriesOptions
        });
    }
});
</script>

</html>