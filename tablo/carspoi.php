<?php

include_once "options.php";

$day = date("d");
$mes = date("m");
$year = date("Y");
$our = date("H");
$min = date("i");
$sec = date("s");

?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="Refresh" content="120"/>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/poi_script.js"></script>
<link rel="stylesheet" type="text/css" href="css/poi_design.css?n=<?php echo mt_rand();?>"/>
</head>
<body>
<div id="all">
<?php
//$qpoi = ads_do($rConn, "SELECT * FROM BD_POI WHERE PGrpName = 'Savushkin'");
$qpoi = $conn->query("SELECT * FROM BD_POI WHERE PGrpName = 'Savushkin'");
?>
<form id="f_poi" method="get">

<select name="sel_poi" id="sel_poi" class="sel_poi" onchange="chpoi()">
<?php
//while (ads_fetch_row($qpoi)){
while($data_poi = $qpoi->fetch( PDO::FETCH_ASSOC )){
	$nmpoi = $data_poi["Caption"];
	$latpoi = $data_poi["Lat"];
	$lonpoi = $data_poi["Lon"];
	$c_poi = $latpoi.",".$lonpoi;
	$sel_f = "";
	if ($c_poi == $_GET['sel_poi']) $sel_f = "selected";
	echo"<option value=\"$c_poi\" $sel_f>$nmpoi</option>";
}
?>
</select>
<?php
if (isset($_GET['sel_type'])){
	$poi_car = $_GET['sel_type'];
}else{
	$poi_car = -1;
}
?>
<select name="sel_type" id="sel_type" class="sel_type" onchange="chpoi()">
	<option value="-1" <?php if ($poi_car == -1) echo "selected";?>>Все машины</option>
	<option value="0" <?php if ($poi_car == 0) echo "selected";?>>Грузовики</option>
	<option value="1" <?php if ($poi_car == 1) echo "selected";?>>Молоковозы</option>
</select>
<table id="poitbl">
	<tr><th>Машина</th><th>Прибытие план</th><th>Прибытие факт</th></tr>
<?php
if ($_GET['sel_poi']){
	$g_coords = explode(",", $_GET['sel_poi']);
	$poi_lat = $g_coords[0];
	$poi_lon = $g_coords[1];
	$coords = "\r\n		<LAT type=\"double\">".$poi_lat."</LAT>\r\n		<LON type=\"double\">".$poi_lon."</LON>\r\n		";
}else{
	$coords = "\r\n		<LAT type=\"double\">52.1127</LAT>\r\n		<LON type=\"double\">23.7651</LON>\r\n		";
}
$msg = "<?phpxml version=\"1.0\" encoding=\"utf-8\"?>\r\n<body login=\"tablo\">\r\n<SetService>\r\n	<GetCarsInPOI>".$coords."<RST type=\"int\">500</RST>\r\n	<PF>".$poi_car."</PF>\r\n	</GetCarsInPOI>\r\n</SetService>\r\n</body>\r\n.\r\n";
include "sock.php";
if (isset ($out)){
	preg_match_all("/<ROW>(.+)<\/ROW>/sUi", $out, $out_car);
	$pi = 0;
	foreach ($out_car[1] as $k => $v){
		$pi++;
		if ($pi%2 == 0){
			$grtr = '';
		}else{
			$grtr = ' class="grtr"';
		}
		preg_match("/<ANUM>(.+)<\/ANUM>/sUi", $v, $car_anum);
		preg_match("/<DTPLAN>(.+)<\/DTPLAN>/sUi", $v, $car_dtplan);
		preg_match("/<DTFACT>(.+)<\/DTFACT>/sUi", $v, $car_dtfact);
		echo "<tr".$grtr."><td>$car_anum[1]</td><td>$car_dtplan[1]</td><td>$car_dtfact[1]</td></tr>";
	}
}
?>
</table>
<div class="link_to_prop"><input type="button" value="Настройки отображения" onclick="parent.location.href='index.php?exit=1'"/></div>
</form>
<?php
//print_r($_GET);
?>
</div>
</body>
</html>