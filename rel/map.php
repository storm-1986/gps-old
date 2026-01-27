<?php
include_once "../options.php";

$day = date("d");
$mes = date("m");
$year = date("Y");
$our = date("H");
$min = date("i");
$sec = date("s");

$url = $_SERVER['QUERY_STRING'];
// echo $url;
parse_str($url, $res);

$cmd = $res['cmd'];
//echo $cmd;		// Тест вывод команд из урл

if (isset($res['minlat'], $res['minlon'], $res['maxlat'], $res['maxlon'])){
	$s_minlat = $res['minlat'] * 1;
	$s_minlon = $res['minlon'] * 1;
	$s_maxlat =  $res['maxlat'] * 1;
	$s_maxlon = $res['maxlon'] * 1;
	// unset ($minlat, $minlon, $maxlat, $maxlon);
}

?>
<!doctype html>
<html lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Карта маршрутов</title>
<link rel="stylesheet" type="text/css" href="css/r_design.css?fff=1"/>
<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/jquery-ui.css"/>
<link rel="stylesheet" href="css/leaflet.css"/>
<link rel="stylesheet" href="css/L.Control.ZoomDisplay.css"/>
<link rel="stylesheet" href="css/Control.Coordinates.css"/>
<link rel="stylesheet" href="css/font-awesome.css"/>
<link rel="stylesheet" href="css/leaflet.awesome-markers.css"/>
<link rel="stylesheet" href="css/leaflet.draw.css" />
<link rel="stylesheet" href="css/leaflet.measurecontrol.css" />
<link rel="stylesheet" href="css/leaflet.label.css" />

<script type="text/javascript" src="js/leaflet.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.canvasjs.min.js"></script>
<script type="text/javascript" src="js/leaflet.awesome-markers.js"></script>
<script type="text/javascript" src="js/r_script.js"></script>
<script type="text/javascript" src="js/leaflet.draw.js"></script>
<script type="text/javascript" src="js/leaflet.measurecontrol.js"></script>

<script type="text/javascript" src="js/Control.Coordinates.js"></script>
<script type="text/javascript" src="js/leaflet.polylineDecorator.js"></script>
<script type="text/javascript" src="js/L.Control.ZoomDisplay.js"></script>
<script type="text/javascript" src="js/leaflet.label.js"></script>
<!-- bootstrap -->
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>
<body onload="init()">
<?php
	$hidelist = '';
?>
<div id="map"></div>

<div class="toast hide" id="graph" style="position: absolute; bottom: 395px; right: 0px; z-index: 10000;">
    <div class="toast-header">
        <strong class="mr-auto">График расхода топлива</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body">
    	<div id="topl_chart"></div>
    </div>
</div>

<div class="toast hide" id="tmpr" style="position: absolute; bottom: 300px; right: 0px; z-index: 10000;">
    <div class="toast-header">
        <strong class="mr-auto">График температуры</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body">
    	<div id="tmpr_chart"></div>
    </div>
</div>

<div class="toast hide" id="skor" style="position: absolute; bottom: 40px; right: 0px; z-index: 10000;">
    <div class="toast-header">
        <strong class="mr-auto">График скорости</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body">
        <div id="skor_chart"></div>
    </div>
</div>

<div id="car_list" <?php echo $hidelist; ?>></div>
<img src="images/ajax-loader.gif" id="loader"/>
<?php

function selcolor($i_col){
	if ($i_col == 0) $trcolor = "blue";		// Цвет трека
	elseif ($i_col == 1) $trcolor = "red";
	elseif ($i_col == 2) $trcolor = "black";
	elseif ($i_col == 3) $trcolor = "green";
	elseif ($i_col == 4) $trcolor = "indigo";
	elseif ($i_col == 5) $trcolor = "darkred";
	elseif ($i_col == 6) $trcolor = "darkblue";
	elseif ($i_col == 7) $trcolor = "darkgoldenrod";
	elseif ($i_col == 8) $trcolor = "dimgray";
	elseif ($i_col == 9) $trcolor = "darkorange";
	elseif ($i_col == 10) $trcolor = "mediumorchid";
	elseif ($i_col == 11) $trcolor = "olive";
	elseif ($i_col == 12) $trcolor = "mediumslateblue";
	elseif ($i_col == 13) $trcolor = "sienna";
	elseif ($i_col == 14) $trcolor = "teal";
	elseif ($i_col == 15) $trcolor = "deepskyblue";
	elseif ($i_col == 16) $trcolor = "indianred";
	elseif ($i_col == 17) $trcolor = "mediumseagreen";
	elseif ($i_col == 18) $trcolor = "gold";
	else $trcolor = "darkviolet";
	return $trcolor;
}

function distance($lat1, $lon1, $lat2, $lon2){	// Дистанция между точками для расчета прострелов
  $r = 6371000;
  $res_dist = Sin(deg2rad($lat1)) * Sin(deg2rad($lat2)) + Cos(deg2rad($lat1)) * Cos(deg2rad($lat2)) * Cos(deg2rad($lon1 - $lon2));
  $res_dist = $r * acos($res_dist);
  return $res_dist;
}


preg_match_all("/(\w+)\(/sUi", $cmd, $command);
//print_r ($command[1]);
$tr_razd = explode("marker", $cmd);
//print_r ($tr_razd);	

foreach ($command[1] as $com){
if ($com == 'track'){	// Для вывода маршрутов
	preg_match_all("/\((.+)\)/sUi", $tr_razd[0], $out_cmd);
//	print_r($out_cmd[1]);
	$tr_arr = array();
	$tr_karr = array();
	$i = 0;
	foreach ($out_cmd[1] as $v){
		$tr_params = explode(";", $v);
		if ($i == 0){
			$tr_karr = $tr_params;
		}else{
			$tr_arr[] = array_combine($tr_karr, $tr_params);
		}
		$i++;
	}
	//print_r($tr_arr);		// итоговый многомерный массив с треками
	$i = 0;
	$i_col = 0;
	$trscript = '';
	$x = '';
	foreach ($tr_arr as $v){
		$anum = $v['anum'];
		$tr = $v['tr'];
		$dt = $v['dt'];
		$dtb = $v['dtb'];
		$dte = $v['dte'];
		$fst = $v['fst'];
		$dtbplan = @$v['dtbplan'];
		$shift = $v['shift'];
		$ntrip = $v['ntrip'];
		$tplPar = @$v['tpl'];
		$tmprPar = @$v['tmpr'];
		$skorPar = @$v['skor'];
		$ntripPar = $v['ntrip'];
		include "tracks.php";

		if (isset($s_maxlat,$s_maxlon,$s_minlat,$s_minlon)){	//	поиск макимальных и минимальных координат	//
			$n_maxlat = $s_maxlat;
			$n_maxlon = $s_maxlon;
			$n_minlat = $s_minlat;
			$n_minlon = $s_minlon;			
		}else{
			if ($i == 0){
				$n_maxlat = $maxlat;
				$n_maxlon = $maxlon;
				$n_minlat = $minlat;
				$n_minlon = $minlon;
			}else{
				if ($maxlat > $n_maxlat) $n_maxlat = $maxlat;
				if ($maxlon > $n_maxlon) $n_maxlon = $maxlon;
				if ($minlat < $n_minlat || $n_minlat == '') $n_minlat = $minlat;
				if ($minlon < $n_minlon || $n_minlon == '') $n_minlon = $minlon;
			}
		}
//	echo $n_maxlat." - ".$n_maxlon." ; ".$n_minlat." - ".$n_minlon."<br>";
	if (($tr == 'f' || $tr == 'b' ) && $count > 1){		//////////////////////// ВЫВОД ФАКТИЧЕСКОГО ТРЕКА ////////////////////////////////
//	print_r ($ar_coord);
	$color = selcolor($i_col);
	$tr_coord = "[";
	$dot_time = [];
	$dot_skor = [];
	$prostrel = '';
	$i_pr = 0;
	$graph_skor = "";
	$graph_tmpr = "";
	foreach($ar_coord as $v1){
		$tr_coord .= "[".$v1['lat'].",".$v1['lon']."],";
		$dot_time[] = "'".$v1['dt_time']."'";
		$dot_skor[] = "'".$v1['speed']."'";
		$graph_skor .= "{ x: ".$v1['vremya'].", y: ".$v1['speed']."},";
		if ($v1['tmpr'] > -30 && $v1['tmpr'] < 70){
			$graph_tmpr .= "{ x: ".$v1['vremya'].", y: ".$v1['tmpr']."},";
		}
		if ($i_pr == 0){
				$lat1 = $v1['lat'];
				$lon1 = $v1['lon'];
				$dt1 = $v1['dt_time'];
		}else{
			$lat2 = $v1['lat'];
			$lon2 = $v1['lon'];
			$dt2 = $v1['dt_time'];
/*
			$dist = distance($lat1,$lon1,$lat2,$lon2);
			if ($dist > 50){
				$f_dt1 = substr($dt1,6,4)."-".substr($dt1,3,2)."-".substr($dt1,0,2)." ".substr($dt1,11,5);
				$f_dt2 = substr($dt2,6,4)."-".substr($dt2,3,2)."-".substr($dt2,0,2)." ".substr($dt2,11,5);
				$time1 = strtotime($f_dt1);
				$time2 = strtotime($f_dt2);
				$diff = $time2 - $time1;

				if ($diff > 100){
					echo $dist."м - ".$diff." - ".$lat1." ".$lon1." - ".$lat2." ".$lon2."<br>";
					$prostrel .= "prostr = L.polyline([[".$lat1.",".$lon1."],[".$lat2.",".$lon2."]],{color: 'white', weight: 5, opacity: 1}).addTo(map);";
				}

			}
*/
			$lat1 = $lat2;
			$lon1 = $lon2;
			$dt1 = $dt2;
		}
		$i_pr++;
	}
	$graph_skor = substr($graph_skor, 0,-1);
	$graph_tmpr = substr($graph_tmpr, 0,-1);
	if ($i == 0){
?>
		<script type="text/javascript">
			jQuery(document).ready(function (){
				function onClick(e) {
					var p_num = e.dataPointIndex;
					console.log(e.dataPoint.x);
					var tmp_c_lat = Object.values(jQuery(result.track[p_num]['lat']));
					var tmp_c_lon = Object.values(jQuery(result.track[p_num]['lon']));
					var tmp_lat = tmp_c_lat[3];
					var tmp_lon = tmp_c_lon[3];
					gotosub_wm(tmp_lat,tmp_lon);
				}
<?php	
		if($skorPar == 1){
?>
				$("#skor").toast({
					autohide: false
				});

				var data = [];
				var dataSeries = { type: "area", xValueType: "dateTime", click: onClick};
				var dataPoints = [<?=$graph_skor;?>];
				dataSeries.dataPoints = dataPoints;
				data.push(dataSeries);

				var options = {
					zoomEnabled: true,
					exportEnabled: true,
					animationEnabled: true,
					title: {
						text: "<?=$anum;?>"
					},
					width: 600,
					height: 300,
					axisY: {
						suffix: " км/ч",
					},
					axisX:{      
						valueFormatString: "DD-MMM HH:mm",
						crosshair: {
							enabled: true,
							snapToDataPoint: true
						}
					},
					data: data
				};
				var skor_chart = new CanvasJS.Chart("skor_chart",options);
				skor_chart.render();

				$(skor_chart.container).click(function(e) {
					var parentOffset = $(this).parent().offset();
					var relX = e.pageX - parentOffset.left - 12;
					var click_dt = Math.round(skor_chart.axisX[0].convertPixelToValue(relX));
					var url = 'map.php';

					jQuery(<?=json_encode($ar_coord)?>).each(function() {
						var track_dt = jQuery(this).attr('vremya');
						if (click_dt <= track_dt){
							gotosub_wm(jQuery(this).attr('lat'),jQuery(this).attr('lon'));
							return false;
						}
					});

				});

				$("#skor").toast('show');

<?php
		}
		if($tmprPar == 1){
?>
				$("#tmpr").toast({
					autohide: false
				});
		
				var data = [];
				var dataSeries = { type: "area", xValueType: "dateTime", click: onClick};
				var dataPoints = [<?=$graph_tmpr;?>];
				dataSeries.dataPoints = dataPoints;
				data.push(dataSeries);
		
				var options = {
					zoomEnabled: true,
					exportEnabled: true,
					animationEnabled: true,
					title: {
						text: "<?=$anum;?>"
					},
					width: 600,
					height: 300,
					axisY: {
						suffix: " °C",
					},
					axisX:{      
						valueFormatString: "DD-MMM HH:mm",
						crosshair: {
							enabled: true,
							snapToDataPoint: true
						}
					},
					data: data
				};
				var tmpr_chart = new CanvasJS.Chart("tmpr_chart",options);
				tmpr_chart.render();
		
				$(tmpr_chart.container).click(function(e) {
					var parentOffset = $(this).parent().offset();
					var relX = e.pageX - parentOffset.left - 12;
					var click_dt = Math.round(tmpr_chart.axisX[0].convertPixelToValue(relX));
					var url = 'map.php';
		
					jQuery(<?=json_encode($ar_coord)?>).each(function() {
						var track_dt = jQuery(this).attr('vremya');
						if (click_dt <= track_dt){
							gotosub_wm(jQuery(this).attr('lat'),jQuery(this).attr('lon'));
							return false;
						}
					});
		
				});
		
				$("#tmpr").toast('show');
		
<?php
		}
		if ($tplPar == 1 && count($ar_topl) > 0){
			$dataPoints1 = "";
			$dataPoints2 = "";
			foreach($ar_topl as $v2){
				$dataPoints1 .= "{ x: ".$v2['vremya'].", y: ".$v2['yroven']."},";
				$dataPoints2 .= "{ x: ".$v2['vremya'].", y: ".$v2['s_yroven']."},";
			}
			$graph_skor = substr($graph_skor, 0,-1);
?>
			$("#graph").toast({
				autohide: false
			});

			var dataPoints1 = [<?=$dataPoints1;?>];
			var dataPoints2 = [<?=$dataPoints2;?>];

			var options = {
				zoomEnabled: true,
				exportEnabled: true,
				animationEnabled: true,
				title: {
					text: "<?=$anum;?>"
				},
				width: 600,
				height: 300,
				axisY: {
					title: "Топливо, %",
					includeZero: true,
				},
				axisY2: {
					title: "Скорость, км/ч",
				},
				axisX:{      
					valueFormatString: "DD.MM HH:mm",
					crosshair: {
						enabled: true,
						snapToDataPoint: true
					}
				},
				legend: {
					verticalAlign: "top",
					horizontalAlign: "right",
					dockInsidePlotArea: true
				},
				toolTip: {
					shared: true
				},
				data: [
				{
					name: "Топливо % (реал)",
					color: "rgba(0, 159, 217, 0.9)",
					showInLegend: true,
					legendMarkerType: "square",
					type: "splineArea",
					xValueType: "dateTime",
					click: onClick,
					dataPoints: dataPoints1,
				},
				{
					name: "Топливо % (сглаж)",
					showInLegend: true,
					color: "red",
					legendMarkerType: "square",
					type: "spline",
					xValueType: "dateTime",
					click: onClick,
					dataPoints: dataPoints2,
				}
				]
			};
			var topl_chart = new CanvasJS.Chart("topl_chart", options);
			topl_chart.render();

			$(topl_chart.container).click(function(e) {
				var parentOffset = $(this).parent().offset();
				var relX = e.pageX - parentOffset.left - 12;
				  var click_dt = Math.round(topl_chart.axisX[0].convertPixelToValue(relX));

				  jQuery(<?=json_encode($ar_topl)?>).each(function() {
					var track_dt = jQuery(this).attr('vremya');
					if (click_dt <= track_dt){
						gotosub_wm(jQuery(this).attr('lat'),jQuery(this).attr('lon'));
						return false;
					}
				});

			});
			$("#graph").toast('show');
			
<?php
		}
?>
			})
		</script>
<?php
	}
	unset($ar_coord);
	$tr_coord = substr($tr_coord, 0, -1);
	$tr_coord .= "]";
	$dot_time = "[" . implode(",", $dot_time) . "]";
	$dot_skor = "[" . implode(",", $dot_skor) . "]";
//	print_r ($tr_coord);
	$trscript .= "
	var ar_time".$i." = ".$dot_time.";
	var ar_skor".$i." = ".$dot_skor.";
	arrow".$i." = L.polyline(".$tr_coord.",{color: '".$color."', weight: 4, opacity: 0.8}).addTo(map);

	    L.polylineDecorator(arrow".$i.", {
        patterns: [
            {offset: 25, repeat: 80, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {color: '".$color."', fillOpacity: 0.8, weight: 0}})}
        ]
    	}).addTo(map);
        arrow".$i.".addEventListener('click', function(e) {
            var index = _getClosestPointIndex(e.latlng, ".$tr_coord.");
            var popup = L.popup()
                .setLatLng(new L.latLng(".$tr_coord."[index]))
                .setContent('<b>".$anum."</b> - фактический маршрут<br />дата и время: '+ar_time".$i."[index]+'<br />скорость: '+ar_skor".$i."[index]+' км/ч')
                .openOn(self.map);
});
".$prostrel;


	if ($fst == 1){							///////////////////////////// ПОКАЗЫВАТЬ ФАКТИЧЕСКИЕ СТОЯНКИ /////////////////////////////////
		
		$msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<body>\n<GWNAV>\n	<GetRoutePoints>\n		<ANUM type=\"string\">".$anum."</ANUM>\n		<DTBEG type=\"string\">".$sgod.$smes.$sch.$sour.$smin."00</DTBEG>\n		<DTEND type=\"string\">".$pogod.$pomes.$poch.$poour.$pomin."00</DTEND>\n		<MINTIME type=\"string\">60</MINTIME>\n		<MINPATH type=\"string\">50</MINPATH>\n	</GetRoutePoints>\n</GWNAV>\n</body>\r\n.\r\n";

		include "sock.php";
		if (isset ($out)){
			$find1 = "<Error>";
			$find2 = "</Error>";
			$pos1 = stripos($out, $find1);
			$pos2 = stripos($out, $find2);
			$resout = substr($out, $pos1 + 7, $pos2 - $pos1 - 7);
			if ($resout == ''){
				preg_match_all("/<Row>(.+)<\/Row>/sUi", $out, $out_arr);
				$fstopm = "fact_stops_LayerGroup = L.layerGroup();";
				$fmi = 0;
				foreach ($out_arr[1] as $k => $v){
	//				echo "<br>Ключ ".$k.": ".$v."<br><br>";
					preg_match_all("/<DTStop>([\d]{14})<\/DTStop>/", $v, $out_arr1);
					if (isset($out_arr1[1][0])){
						$dtstop = substr($out_arr1[1][0], 6, 2) . "." . substr($out_arr1[1][0], 4, 2) . "." . substr($out_arr1[1][0], 0, 4) . " " . substr($out_arr1[1][0], 8, 2) . ":" . substr($out_arr1[1][0], 10, 2) . ":" . substr($out_arr1[1][0], 12, 2);
					}
					else{
						$dtstop = '-';
					}
					preg_match_all("/<DTEnd>([\d]{14})<\/DTEnd>/", $v, $out_arr2);
					if (isset($out_arr2[1][0])){
						$dtend = substr($out_arr2[1][0], 6, 2) . "." . substr($out_arr2[1][0], 4, 2) . "." . substr($out_arr2[1][0], 0, 4)." ".substr($out_arr2[1][0], 8, 2) . ":" . substr($out_arr2[1][0], 10, 2) . ":" . substr($out_arr2[1][0], 12, 2);
					}
					else{
						$dtend = '-';
					}
					preg_match_all("/<Lat>(.+)<\/Lat>/", $v, $out_arr3);
//					echo $out_arr3[1][0]."<br>";
					preg_match_all("/<Lon>(.+)<\/Lon>/", $v, $out_arr4);
//					echo $out_arr4[1][0]."<br>";
					preg_match_all("/<Address>(.+)<\/Address>/", $v, $out_arr7);
//					echo $out_arr7[1][0]."<br>";
					$dotAddr = '';
					if (isset($out_arr7[1][0])){
						$dotAddr = $out_arr7[1][0];
					}
					$fstopm .= "var fm = new L.marker([" . $out_arr3[1][0] . "," . $out_arr4[1][0] . "], {icon: L.AwesomeMarkers.icon({icon: '', markerColor: '" . $color . "', prefix: 'fa', html: 'C'}),riseOnHover: true}).bindPopup(\"<b>Стоянка $anum</b><br>	Адрес: $dotAddr<br>Начало: $dtstop<br>Конец: $dtend\"); fact_stops_LayerGroup.addLayer(fm);";
					$fmi++;
				}
				$fstopm .= "fact_stops_LayerGroup.addTo(map); layerControl.addOverlay(fact_stops_LayerGroup, 'Фактические стоянки $anum');";
			}else{
				echo "<script type=\"text/javascript\">alert('$resout');</script>";
			}
		}else{
			//	if ($fl_err !== 1) echo "<script type=\"text/javascript\">alert('Нет ответа от сервера определения стоянок');</script>";
				$fl_err = 1;
		}
	}											///////////////////////////  КОНЕЦ КОДА ФАКТИЧЕСКИЕ СТОЯНКИ ////////////////////////
	unset($tr_coord);
	unset($dot_time);
	$trscript .= $fstopm;
//	echo $tr_coord;
	$x .= "<div id=\"cl_$color\" class=\"cl_all\" onClick=\"gototr(arrow$i)\">$anum</div>";
	$i_col++;
	}
	if (($tr == 'f' || $tr == 'b') && $count < 2){
		$x .= "<div id=\"cl_$color\" class=\"cl_all\">Нет фактического трека для $anum</div>";
		$i_col++;
	}	
	if (($tr == 'p' || $tr == 'b') && $pi > 1){		//////////////////////// ВЫВОД ПЛАНОВОГО ТРЕКА ////////////////////////////////
	$color = selcolor($i_col);
//	print_r ($ar_pcoord);
	$tr_pcoord = "[";
	$dot_ptime = [];
	foreach($ar_pcoord as $p1){
		$tr_pcoord .= "[" . $p1['lat'] . "," . $p1['lon'] . "],";
		$dot_ptime[] = "'" . $p1['date'] . "'";
	}
	unset($ar_pcoord);
	$tr_pcoord = substr($tr_pcoord, 0, -1);
	$tr_pcoord .= "]";
	$dot_ptime = "[" . implode(",", $dot_ptime) . "]";
	$trscript .= "
	var ar_ptime".$i." = ".$dot_ptime.";
	parrow".$i." = L.polyline(".$tr_pcoord.",{color: '".$color."', weight: 4, smoothFactor: 1, opacity: 0.7}).addTo(map);
	    L.polylineDecorator(parrow".$i.", {
        patterns: [
            {offset: 25, repeat: 80, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {color: '".$color."', fillOpacity: 0.8, weight: 0}})}
        ]
    	}).addTo(map);
        parrow".$i.".addEventListener('click', function(e) {
            var index = _getClosestPointIndex(e.latlng, ".$tr_pcoord.");
            var popup = L.popup()
                .setLatLng(new L.latLng(".$tr_pcoord."[index]))
                .setContent('<b>".$anum."</b> - плановый маршрут<br />дата и время: '+ar_ptime".$i."[index])
                .openOn(self.map);
});
		";
//	echo $tr_pcoord;

												/////////////////////////////// ПЛАНОВЫЕ СТОЯНКИ //////////////////////////////////
$vpresultid = $conn->query($vpquery);
$vpi = 0;
$sub_list = '';
$pstop_m = "plan_stops_LayerGroup = L.layerGroup();";
while($data_vprid = $vpresultid->fetch( PDO::FETCH_ASSOC )){
	$vpi++;
	$vplatt = $data_vprid["LAT"];
	$vplongt = $data_vprid["LON"];
	$vpddate = $data_vprid["DT"];
	$vpddate = substr($vpddate, 0, 19);
	$vptype = $data_vprid["PTYPE"];
	if ($vptype == 1){
		$type_dot = "Погрузка";
	}else{
		$type_dot = "Разгрузка";
	}

	$adr = $data_vprid["ADDRESS"];
	$adr = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $adr);
	$adr = trim($adr);
	$adr = htmlspecialchars($adr, ENT_QUOTES);
	if ($adr > '' && $adr[1] == '-'){
		$afternum = $adr[0];
		$vpi2 = $vpi."-".$afternum;
	}else{
		$vpi2 = $vpi;
	}
	if (strpbrk($adr, '~') == false){
		$add_tooltip = "";
		$form_adr2 = $adr;
	}else{
		$form_adr = explode("~",$adr);
		if ($form_adr[0] > ''){
			$add_tooltip = ".bindLabel('$form_adr[0]', { noHide: true })";
			$form_adr2 = $form_adr[1];
		}else{
			$add_tooltip = "";
		}
	}
	$pstop_txt = "<b>".$type_dot." ".$car_id."</b><br/>Время: ".substr($vpddate,8,2).".".substr($vpddate,5,2).".".substr($vpddate,0,4)." ".substr($vpddate,11,5)."<br/>".$form_adr2;
	$sub_list .= "<span class=\"insub\" onclick=\"gotosub($vplatt,$vplongt)\"><b>".$vpi."</b> ".$adr."</span><br/>";
	if ($pi > 1) $pstop_m .= "var pm = new L.marker([".$vplatt.", ".$vplongt."],{icon: L.AwesomeMarkers.icon({icon: '', markerColor: '$color', prefix: 'fa', html: '$vpi2'}),zIndexOffset:10}).bindPopup(\"".$pstop_txt."\")$add_tooltip;
	plan_stops_LayerGroup.addLayer(pm);";
}

$pstop_m .= "plan_stops_LayerGroup.addTo(map); layerControl.addOverlay(plan_stops_LayerGroup, 'Плановые стоянки $car_id');";

$x .= "<div id=\"cl_$color\" class=\"cl_all\" onClick=\"gototr(parrow$i),sh_sub($i)\">$anum плановый</div><div id=\"sub_$i\" class=\"sub_cl_all\"></div>";
		$i_col++;
		
	$trscript .= $pstop_m;
	$trscript .= "$('#sub_$i').html('$sub_list');";
	}
	

	if ($tr == 'p' && $pi < 2){
		$x .= "<div id=\"cl_$color\" class=\"cl_all\">Нет планового трека для $anum</div>";
		$i_col++;
	}
	if ($tr == 'b' && $pi < 2){
		$x .= "<div id=\"cl_$color\" class=\"cl_all\">Нет планового трека для $anum</div>";
		$i_col++;
	}
//		if ($fst == 1 && isset($out) && $resout == '') $fn_tr .= "showstops('$anum'); ";
		$i++;
	}

?>
<script type="text/javascript">
	jQuery('#car_list').html('<?php echo $x; ?>');
</script>
<?php
}elseif ($com == 'marker'){										/////////////////////// Для вывода маркеров //////////////////////////////////////
	preg_match_all("/\((.+)\)/sUi", $tr_razd[1], $out_cmd);
//	print_r($out_cmd[1]);
	$mr_arr = array();
	$mr_karr = array();
	$i = 0;
	foreach ($out_cmd[1] as $v){
		$mr_params = explode(";", $v);
		if ($i == 0){
			$mr_karr = $mr_params; 
		}else{
			$mr_arr[] = array_combine($mr_karr, $mr_params);
		}
		$i++;
	}
	$i = 0;
	foreach ($mr_arr as $v){
		$mlat = $v['mlat'];
		$mlon = $v['mlon'];
		$mcolor = strtolower($v['color']);
		if (intval($v['nm']) > 0){
			$lbl = $v['nm'];			
		}else{
			$lbl_b64 = str_ireplace("_", "+", $v['nm']);
			$lbl = base64_decode($lbl_b64);
		}
		if ($v['mtxt'] > ''){
			if ($_SERVER['HTTP_HOST'] == "bpr_serv"){
				$mtext = $v['mtxt'];				
			}else{
				$test_b64 = str_ireplace("_", "+", $v['mtxt']);
				$mtext = base64_decode($test_b64);
			}
			$add_popup = ".bindPopup(\"".$mtext."\").openPopup()";
		}else{
			$add_popup = "";
		}
		$radius = $v['rad'];
		if ($radius > ''){
			$add_circle = "L.circle([".$mlat.", ".$mlon."], ".$radius.",{weight: 1}).addTo(map);";
		}else{
			$add_circle = "";
		}
		if ($test_b64 = base64_decode($test_b64_z, true)){
			$mtext = $test_b64;
		}else {
			$mtext = $v['mtxt'];
		}
		if (isset($s_maxlat,$s_maxlon,$s_minlat,$s_minlon)){	//	поиск макимальных и минимальных координат	//
			$n_maxlat_m = $s_maxlat;
			$n_maxlon_m = $s_maxlon;
			$n_minlat_m = $s_minlat;
			$n_minlon_m = $s_minlon;			
		}else{
		if ($i == 0){		// Алгоритм определения масштаба и центра карты
			$n_maxlat_m = $mlat;
			$n_maxlon_m = $mlon;
			$n_minlat_m = $mlat;
			$n_minlon_m = $mlon;
		}else{
			if ($mlat > $n_maxlat_m) $n_maxlat_m = $mlat;
			if ($mlon > $n_maxlon_m) $n_maxlon_m = $mlon;
			if ($mlat < $n_minlat_m) $n_minlat_m = $mlat;
			if ($mlon < $n_minlon_m) $n_minlon_m = $mlon;
		}
		}
		$getmarkers .= "L.marker([".$mlat.", ".$mlon."],{icon: L.AwesomeMarkers.icon({icon: '', markerColor: '$mcolor', prefix: 'fa', html: '$lbl'}),zIndexOffset:10000}).addTo(map)".$add_popup."; ".$add_circle;
		$i++;
	}
}
}
?>
<script type="text/javascript">
function tom(){
	<?php
	if (isset($trscript)&&!isset($getmarkers)){
		$trscript .= "map.fitBounds([[$n_maxlat, $n_maxlon],[$n_minlat, $n_minlon]]);";
		echo $trscript;
	}elseif(!isset($trscript)&&isset($getmarkers)){
		$getmarkers .= "map.fitBounds([[$n_maxlat_m, $n_maxlon_m],[$n_minlat_m, $n_minlon_m]]);";
		if ($i > 1) $getmarkers .= "var z = map.getZoom();z--;map.setZoom(z);";
		echo $getmarkers;			
	}elseif(isset($trscript)&&isset($getmarkers)){
		if ($n_maxlat > $n_maxlat_m) $n_maxlat_m = $n_maxlat;
		if ($n_maxlon > $n_maxlon_m) $n_maxlon_m = $n_maxlon;
		if ($n_minlat < $n_minlat_m) $n_minlat_m = $n_minlat;
		if ($n_minlon < $n_minlon_m) $n_minlon_m = $n_minlon;
		$getmarkers .= "map.fitBounds([[$n_maxlat_m, $n_maxlon_m],[$n_minlat_m, $n_minlon_m]]);";
		echo $trscript;
		echo $getmarkers;			
	}
	?> 
}
function gotosub_wm(lat,lon){
	if (window.skor_marker){
		map.removeLayer(skor_marker)
	}
    skor_marker = new L.Marker(new L.LatLng(lat, lon));
    map.addLayer(skor_marker);
	map.setView(new L.LatLng(lat, lon),17);
}
</script>
</body>
</html>