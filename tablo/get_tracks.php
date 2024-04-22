<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");
/*
if ($rConn == False){
	$strErrCode = ads_error();
	$strErrString = ads_errormsg();
//	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\r\n";
	exit;
}
*/
$anum = $_POST['car'];
$dt_begin = $_POST['date_b'];
$stops = $_POST['stops'];
//$anum = 'AE67861';

$day = date("d");
$mes = date("m");
$year = date("Y");

$mainq = "SELECT * FROM BD_PLAN WHERE ANUM = '".$anum."' AND DTBEG  = '".$dt_begin."' ORDER BY DT";

//$qpltrack = ads_do($rConn,$mainq);
$qpltrack = sqlsrv_query($conn, $mainq);	// запрос планового маршрута

$i = 0;
//while (ads_fetch_row($qpltrack)){
while($data_track = sqlsrv_fetch_array($qpltrack, SQLSRV_FETCH_ASSOC)){
	$platt = $data_track["LAT"];
	$plongt = $data_track["LON"];
	$pddate = $data_track["DT"];

	$ar_pcoord[$i]['lat'] = $platt;
	$ar_pcoord[$i]['lon'] = $plongt;
	$ar_pcoord[$i]['date'] = substr($pddate,8,2).".".substr($pddate,5,2).".".substr($pddate,0,4)." ".substr($pddate,11,5);
	$i++;		
}

if ($i > 0){
/*
SELECT * FROM BD_PLAN WHERE (DTBEG  = '20180227' and ANUM = 'AE71611' AND PTYPE <> 5) or (DTBEG  = '20180227' and ANUM = '_AE71611') ORDER BY DT
*/
	$text_qplstops = "SELECT * FROM BD_PLAN WHERE (DTBEG  = '".$dt_begin."' AND ANUM = '".$anum."' AND PTYPE <> 5) ORDER BY DT";
//	$qplstops = ads_do($rConn,$text_qplstops);
	$qplstops = sqlsrv_query($conn, $text_qplstops);		// запрос стоянок планового маршрута
	$count = 0;
	$_count = 0;
//	while (ads_fetch_row($qplstops)){
	while($data_stops = sqlsrv_fetch_array($qplstops, SQLSRV_FETCH_ASSOC)){
		$db_anum = trim($data_stops["ANUM"]); 
		$slatt = $data_stops["LAT"];
		$slongt = $data_stops["LON"];
		$sddate = $data_stops["DT"];
		$point_type = $data_stops["PTYPE"];
		$smena = $data_stops["SHIFT"];
		$st_adr = iconv("CP1251", "UTF-8", $data_stops["ADDRESS"]);
		if ($point_type == 1){
			$stop_type = 'Погрузка';
		}else{
			$stop_type = 'Разгрузка';
		}
		if ($db_anum{0} !== '_'){
			$ar_stops[$count]['lat'] = $slatt;
			$ar_stops[$count]['lon'] = $slongt;
			$ar_stops[$count]['stat'] = $stops;
			$ar_stops[$count]['adr'] = "Смена: ".$smena." ".$st_adr;
			$ar_stops[$count]['text'] = "<b>".$stop_type.":</b><br/>".$st_adr."<br>Планируемое ".substr($sddate, 8, 2).".".substr($sddate, 5, 2).".".substr($sddate, 0, 4)." ".substr($sddate, 11, 8);
			$count++;
		}else{
			$ar_stops2[$_count]['lat'] = $slatt;
			$ar_stops2[$_count]['lon'] = $slongt;
			$ar_stops2[$_count]['dt'] = "<br/>Факт (прогноз) ".substr($sddate, 8, 2).".".substr($sddate, 5, 2).".".substr($sddate, 0, 4)." ".substr($sddate, 11, 8);
			$_count++;
		}
	}
	for ($i = 0; $i < $_count; $i++){
		if ($ar_stops[$i]['lat'] == $ar_stops2[$i]['lat'] && $ar_stops[$i]['lon'] == $ar_stops2[$i]['lon']){
			$ar_stops[$i]['text'] .= $ar_stops2[$i]['dt'];
		}else{
			for ($i2 = 0; $i2 < $_count; $i2++){
				if ($ar_stops[$i]['lat'] == $ar_stops2[$i2]['lat'] && $ar_stops[$i]['lon'] == $ar_stops2[$i2]['lon'])	$ar_stops[$i]['text'] .= $ar_stops2[$i2]['dt'];	
			}
		}
	}
	$last_q = "SELECT TOP 1 * FROM BD_GBR WHERE ANUM = '".$anum."' AND DDATE = '".$year."-".$mes."-".$day."' ORDER BY DTSTAMP DESC";
	$qrpos = sqlsrv_query($conn, $last_q);		// запрос последних координат
	$i = 0;
	while($data_last = sqlsrv_fetch_array($qrpos, SQLSRV_FETCH_ASSOC)){
		$rlat = $data_last["LATT"];
		$rlon = $data_last["LONGT"];
		$rdate = $data_last["DDATE"];
		$rtime = $data_last["TTIME"];
		$rspeed = $data_last["VEL"];
		$i++;
	}
	if ($i == 0){	// Берем координаты из ADS если нет в SQL
		$qrpos = ads_do($rConn,"SELECT TOP 1 START AT 1 LASTLAT, LASTLONG, LASTDATE, LASTTIME, LASTSPEED FROM GPS_LAST WHERE ANUM = '".$anum."' ORDER BY LASTDATE+LASTTIME DESC");		// запрос последних координат
		// $qrpos = ads_do($rConn,"SELECT TOP 1 START AT 1 LATT, LONGT, DDATE, TTIME, VEL FROM BD_GBR WHERE ANUM = '".$anum."' ORDER BY DDATE+TTIME DESC");		// запрос последних координат

		while (ads_fetch_row($qrpos)){
			$rlat = ads_result($qrpos, "LASTLAT");
			$rlon = ads_result($qrpos, "LASTLONG");
			$rdate = ads_result($qrpos, "LASTDATE");
			$rtime = ads_result($qrpos, "LASTTIME");
			$rspeed = ads_result($qrpos, "LASTSPEED");
		}
	}
	$rdttime = "<b>".$anum."</b><br/>".substr($rdate, 8, 2).".".substr($rdate, 5, 2).".".substr($rdate, 0, 4)." ".$rtime."<br/>Скорость: ".$rspeed." км/ч";

	$result = array('type'=>'success','tracks'=>$ar_pcoord,'pstops'=>$ar_stops,'r_lat'=>$rlat,'r_lon'=>$rlon,'r_text'=>$rdttime,'query'=>$text_qplstops,'test_q'=>$text_qplstops);
}else{
	$result = array('type'=>'error','msg'=>"Нет планового маршрута");
}

print json_encode($result);
ads_close($rConn);
?>