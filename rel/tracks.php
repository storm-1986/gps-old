<?php

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

if (isset($ntripPar) && $ntripPar > 0){
	$qtrip =  "AND NTRIP = $ntripPar";
}else{
	$qtrip =  "";
}
//////////////////////////////////////////////////// ЗАПРОСЫ ///////////////////////////////////////////////////////////////

$sdate = $sgod.$smes.$sch;
$podate = $pogod.$pomes.$poch;

$sdate_f = $sgod."-".$smes."-".$sch;
$podate_f = $pogod."-".$pomes."-".$poch;

if ($sdate == $podate){
// Запрос для фактического трека, если выбран 1 день

	// sql	
	$sql_query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."') AND (TTIME >= '".$sour.":".$smin.":00') AND (TTIME <= '".$poour.":".$pomin.":00') ORDER BY DTSTAMP";

	// Запрос по расходу топлива
	$sql_q_topl = "SELECT FUEL, DTSTAMP FROM BD_GBR WHERE ANUM = '".$car_id."' AND DDATE = '".$sdate_f."' AND TTIME >= '".$sour.":".$smin.":00' AND TTIME <= '".$poour.":".$pomin.":00' ORDER BY DTSTAMP";
	
// Запрос для планируемого трека

	if ($dt > ''){		//частный случай при выезде машины ДО даты, заданной в $dtb
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') AND PTYPE = 5 ORDER BY DT";
// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') ORDER BY DT";
	}else{
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (DTBEG = '".$sdate."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') AND PTYPE = 5 ORDER BY DT";
// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') AND (DT >= '".$sgod."-".$smes."-".$sch."T".$sour.":".$smin.":00') AND (DT <= '".$sgod."-".$smes."-".$sch."T".$poour.":".$pomin.":00') ORDER BY DT";
	}

}
else{
// Запрос для фактического трека для 2 и более дней
// sql
	$fq = "(DDATE = '".$sdate_f."') AND (DTSTAMP >= '".$sdate_f." ".$sour.":".$smin.":00')";

	$iii = 0;
	$eq = '';
	$mq = '';
	$mqt = '';
	while ($sdate_f != $podate_f){
		$iii++;
		$date = new DateTime();
		$date->setDate($sgod, $smes, $sch + $iii);
		$sdate_f = $date->format('Y-m-d');
		if ($sdate_f == $podate_f){
			$eq = "(DDATE = '".$sdate_f."') AND (DTSTAMP <= '".$podate_f." ".$poour.":".$pomin.":00')";
		}
		else{
			$mq .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."')";
			$mqt .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."') AND (TYPE_DATA = 34 OR TYPE_DATA = 89)"; // для запроса по топливу
		}
	}
	$sql_query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND ".$fq.$mq." OR (ANUM = '".$car_id."') AND ".$eq." ORDER BY DTSTAMP";

	// Запрос по расходу топлива
	$sql_q_topl = "SELECT DDATA, DTSTAMP FROM BD_ADD_DATA WHERE ANUM = '".$car_id."' AND (TYPE_DATA = 34 OR TYPE_DATA = 89) AND ".$fq.$mqt." OR ANUM = '".$car_id."' AND (TYPE_DATA = 34 OR TYPE_DATA = 89) AND ".$eq."ORDER BY DTSTAMP";

// Запрос для планируемого трека
	if ($dt > ''){		//частный случай при выезде машины ДО даты, заданной в $dtb
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') AND PTYPE = 5 ORDER BY DT";
		// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sgod_dt.$smes_dt.$sch_dt."') ORDER BY DT";
	}else{
		$pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (DTBEG = '".$sdate."') AND PTYPE = 5 ORDER BY DT";
		// Запрос для планируемых стоянок
		$vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." ".$qtrip." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') ORDER BY DT";
	}
}
//////////////////////////////////////////////////// КОНЕЦ ЗАПРОССОВ ///////////////////////////////////////////////////////////////

//////////////////////////////////////////////////// ТРЕК ФАКТИЧЕСКИЙ /////////////////////////////////////////////////////////////////

if ($tr == 'f' || $tr == 'b' ){
// sql
// echo $query;

		$resultid = $conn->query($sql_query);
		$count = 0;
		while($data_rid = $resultid->fetch( PDO::FETCH_ASSOC )){
			$latt = $data_rid["LATT"];
			$longt = $data_rid["LONGT"];
			$ddate = $data_rid["DDATE"];
			$ttime = $data_rid["TTIME"];
			$dt_time = substr($ddate, 8, 2) . "." . substr($ddate, 5, 2) . "." . substr($ddate, 0, 4) . " " . substr($ttime, 0, 5);
			$vrem4skor = strtotime($data_rid["DTSTAMP"]);
			$skor = $data_rid["VEL"];
			$tmpr = $data_rid["TMPR"];

			if ($latt > 40 && $latt < 78 && $longt > 0 && $longt < 190){ // заглушка для координат евразии
				$ar_coord[$count]['lat'] = $latt;
				$ar_coord[$count]['lon'] = $longt;
				$ar_coord[$count]['dt_time'] = $dt_time;
				$ar_coord[$count]['vremya'] = $vrem4skor*1000;
				$ar_coord[$count]['speed'] = $skor;
				$ar_coord[$count]['tmpr'] = $tmpr;

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

	if ($tplPar == 1){
		@$res_topl = $conn->query($sql_q_topl);
		if ($res_topl){
			$tcount = 0;
			while($data_tpl = $res_topl->fetch( PDO::FETCH_ASSOC )){
				$yroven = $data_tpl["FUEL"]*1;
				$vrem = strtotime($data_tpl["DTSTAMP"]);
				$ar_topl[$tcount]['yroven'] = $yroven;
				$ar_topl[$tcount]['vremya'] = $vrem*1000;
				$tcount++;
			}
	
			if (count($ar_topl) > 0){
				// Сглаживание графика топлива
				$tempArr = array();
				// Широта охвата точек для регулировки сглаживания
				$srez = 60;
				foreach ($ar_topl as $key => $val){
					$tempArr[] = $val['yroven'];
				}
			
				for ($i_t = 0; $i_t < count($tempArr); $i_t++) {
					$start = $i_t - round($srez/2);
					$slSrez = $start + $srez;
					if ($slSrez > $srez) $slSrez = $srez;
					if ($start < 0) $start = 0;
					$slice = array();
					$slice = array_slice($tempArr, $start, $slSrez);
					$res = array_sum($slice) / count($slice);
					$ar_topl[$i_t]['s_yroven'] = round($res, 1);
				}
			}
		}
	}
		

}
//////////////////////////////////////////////////// КОНЕЦ ТРЕК ФАКТИЧЕСКИЙ /////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////// ТРЕК ПЛАНОВЫЙ /////////////////////////////////////////////////////////////////
if ($tr == 'p' || $tr == 'b' ){

	//echo $pquery;
	//echo $vpquery;

	$presultid = $conn->query($pquery);

	$pi = 0;
	while($data_prid = $presultid->fetch( PDO::FETCH_ASSOC )){
		$platt = $data_prid["LAT"];
		$plongt = $data_prid["LON"];
		$pddate = $data_prid["DT"];
		$ar_pcoord[$pi]['lat'] = $platt;
		$ar_pcoord[$pi]['lon'] = $plongt;
		$ar_pcoord[$pi]['date'] = substr($pddate, 8, 2) . "." . substr($pddate, 5, 2) . "." . substr($pddate, 0, 4) . "T" . substr($pddate, 11, 5);
		if (isset($count) && $count < 3){ // брать крайние точки при выводе только планового маршрута и при выводе обоих маршрутов, если в фактическом меньше 2 точек или он отсутствует
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
?>