<?php
// удалить потом
$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ($rConn == False){
	$strErrCode = ads_error();
	$strErrString = ads_errormsg();
//	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\r\n";
	exit;
}

if ($dt > ''){
	$sgod_dt = substr($dt,0,4);
	$smes_dt = substr($dt,4,2);
	$sch_dt = substr($dt,6,2);
}
$sgod = substr($dtb,0,4);
$smes = substr($dtb,4,2);
$sch = substr($dtb,6,2);
$sour = substr($dtb,8,2);
$smin = substr($dtb,10,2);
$pogod = substr($dte,0,4);
$pomes = substr($dte,4,2);
$poch = substr($dte,6,2);
$poour = substr($dte,8,2);
$pomin = substr($dte,10,2);
$car_id = $anum;
if ($shift !== '') $qshift = "AND (SHIFT = '".$shift."')";
else $qshift = "";
//////////////////////////////////////////////////// ЗАПРОСЫ ///////////////////////////////////////////////////////////////

$sdate = $sgod.$smes.$sch;
$podate = $pogod.$pomes.$poch;

$sdate_f = $sgod."-".$smes."-".$sch;
$podate_f = $pogod."-".$pomes."-".$poch;

if ($sdate == $podate){
// Запрос для фактического трека, если выбран 1 день

//sql	$query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."') AND (TTIME >= '".$sour.":".$smin.":00') AND (TTIME <= '".$poour.":".$pomin.":00') ORDER BY DTSTAMP";

	$query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND (DDATE = '".$sgod.$smes.$sch."') AND (TTIME >= '".$sour.":".$smin.":00') AND (TTIME <= '".$poour.":".$pomin.":00') ORDER BY DTSTAMP";

// Запрос для планируемого трека

	if ($dt > ''){		//частный случай при выезде машины ДО даты, заданной в $dtb
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') AND PTYPE = 5 ORDER BY DT";
// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') ORDER BY DT";
	}else{
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sdate."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') AND PTYPE = 5 ORDER BY DT";
// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') ORDER BY DT";
	}

}
else{
// Запрос для фактического трека для 2 и более дней
/* sql
$fq = "(DDATE = '".$sdate_f."') AND (DTSTAMP >= '".$sdate_f." ".$sour.":".$smin.":00')";

	$iii = 0;
	while ($sdate_f !== $podate_f){
		$iii++;
		$sdate_f = strftime('%Y-%m-%d',mktime(0,0,0,$smes,$sch+$iii,$sgod,-1));
		if ($sdate_f == $podate_f){
			$eq = "(DDATE = '".$sdate_f."') AND (DTSTAMP <= '".$podate_f." ".$poour.":".$pomin.":00')";
		}
		else{
			$mq .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."')";
		}
	}
	$query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND ".$fq.$mq." OR (ANUM = '".$car_id."') AND ".$eq." ORDER BY DTSTAMP";
*/

$fq = "(DDATE = '".$sdate."') AND (DTSTAMP >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00')";

	$iii = 0;
	while ($sdate !== $podate){
		$iii++;
		$sdate = strftime('%Y%m%d',mktime(0,0,0,$smes,$sch+$iii,$sgod,-1));
		if ($sdate == $podate){
			$eq = "(DDATE = '".$sdate."') AND (DTSTAMP <= '".$pogod."-".$pomes."-".$poch."T".$poour.":".$pomin.":00')";
		}
		else{
			$mq .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate."')";
		}
	}
	$query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND ".$fq.$mq." OR (ANUM = '".$car_id."') AND ".$eq." ORDER BY DTSTAMP";


// Запрос для планируемого трека
	if ($dt > ''){		//частный случай при выезде машины ДО даты, заданной в $dtb
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') AND PTYPE = 5 ORDER BY DT";
		// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') ORDER BY DT";
	}else{
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sdate."') AND PTYPE = 5 ORDER BY DT";
		// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') ORDER BY DT";
	}
}
//////////////////////////////////////////////////// КОНЕЦ ЗАПРОССОВ ///////////////////////////////////////////////////////////////

//////////////////////////////////////////////////// ТРЕК ФАКТИЧЕСКИЙ /////////////////////////////////////////////////////////////////

if ($tr == 'f' || $tr == 'b' ){
/* sql
echo $query;

//	$resultid = ads_do($rConn, $query);
	$resultid = sqlsrv_query($conn, $query);
	if($resultid === false){
		die( FormatErrors( sqlsrv_errors(), true));
	}
	$count = 0;
//	while (ads_fetch_row($resultid)){
	while($data_rid = sqlsrv_fetch_array($resultid, SQLSRV_FETCH_ASSOC)){
		$latt = $data_rid["LATT"];
		$longt = $data_rid["LONGT"];
		$ddate = $data_rid["DDATE"];
		$ttime = $data_rid["TTIME"];
		$dt_time = substr($ddate,8,2).".".substr($ddate,5,2).".".substr($ddate,0,4)." ".substr($ttime,0,5);
		$vrem4skor = strtotime($data_rid["DTSTAMP"]);
		$skor = $data_rid["VEL"];

		if ($latt > 40 && $latt < 78 && $longt > 0 && $longt < 190){ // заглушка для координат евразии
		$ar_coord[$count]['lat'] = $latt;
		$ar_coord[$count]['lon'] = $longt;
		$ar_coord[$count]['dt_time'] = $dt_time;
		$ar_coord[$count]['vremya'] = $vrem4skor*1000;
		$ar_coord[$count]['speed'] = $skor;

		if ($count == 0){
			$maxlat = $latt;
			$minlat = $latt;
			$maxlon = $longt;
			$minlon = $longt;
		}else{
			if ($latt > $maxlat) $maxlat = $latt;
			if ($latt < $minlat) $minlat = $latt; 
			if ($longt > $maxlon) $maxlon = $longt;
			if ($longt < $minlon) $minlon = $longt;	
		}
		$count++;
		}
	}
*/
	$resultid = ads_do($rConn, $query);
	$count = 0;
	while (ads_fetch_row($resultid)){
		$latt = ads_result($resultid, "LATT");
		$longt = ads_result($resultid, "LONGT");
		$ddate = ads_result($resultid, "DDATE");
		$ttime = ads_result($resultid, "TTIME");
		$dt_time = substr($ddate,6,2).".".substr($ddate,4,2).".".substr($ddate,0,4)."T".substr($ttime,0,5);
		$vrem4skor = strtotime(ads_result($resultid, "DTSTAMP"));
		$skor = ads_result($resultid, "VEL");

		if ($latt > 40 && $latt < 78 && $longt > 0 && $longt < 190){ // заглушка для координат евразии
		$ar_coord[$count]['lat'] = $latt;
		$ar_coord[$count]['lon'] = $longt;
		$ar_coord[$count]['dt_time'] = $dt_time;
		$ar_coord[$count]['vremya'] = $vrem4skor*1000;
		$ar_coord[$count]['speed'] = $skor;

		if ($count == 0){
			$maxlat = $latt;
			$minlat = $latt;
			$maxlon = $longt;
			$minlon = $longt;
		}else{
			if ($latt > $maxlat) $maxlat = $latt;
			if ($latt < $minlat) $minlat = $latt; 
			if ($longt > $maxlon) $maxlon = $longt;
			if ($longt < $minlon) $minlon = $longt;	
		}
		$count++;
		}
	}
}
//////////////////////////////////////////////////// КОНЕЦ ТРЕК ФАКТИЧЕСКИЙ /////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////// ТРЕК ПЛАНОВЫЙ /////////////////////////////////////////////////////////////////
if ($tr == 'p' || $tr == 'b' ){

//echo $pquery;
//echo $vpquery;

//$presultid = ads_do($rConn, $pquery);
$presultid = sqlsrv_query($conn, $pquery);
if($presultid === false){
	die( FormatErrors( sqlsrv_errors(), true));
}

$pi = 0;
//while (ads_fetch_row($presultid)){
while($data_prid = sqlsrv_fetch_array($presultid, SQLSRV_FETCH_ASSOC)){
	$platt = $data_prid["LAT"];
	$plongt = $data_prid["LON"];
	$pddate = $data_prid["DT"];
	$ar_pcoord[$pi]['lat'] = $platt;
	$ar_pcoord[$pi]['lon'] = $plongt;
	$ar_pcoord[$pi]['date'] = substr($pddate,8,2).".".substr($pddate,5,2).".".substr($pddate,0,4)."T".substr($pddate,11,5);
	if ($count < 3){ // брать крайние точки при выводе только планового маршрута и при выводе обоих маршрутов, если в фактическом меньше 2 точек или он отсутствует
		if ($pi == 0){
		$maxlat = $platt;
		$minlat = $platt;
		$maxlon = $plongt;
		$minlon = $plongt;
	}else{
		if ($platt > $maxlat) $maxlat = $platt;
		if ($platt < $minlat) $minlat = $platt; 
		if ($plongt > $maxlon) $maxlon = $plongt;
		if ($plongt < $minlon) $minlon = $plongt;
	}
	}
	$pi++;
}
}

//////////////////////////////////////////////////// КОНЕЦ ТРЕК ПЛАНОВЫЙ /////////////////////////////////////////////////////////////////

//ads_close($rConn);
?>