<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$ver = $_GET['ver'];
$ftr = $_GET['ftr'];

if ($ver == 1){

$su_name = $_GET['su_name'];
$car_id = $_GET['car'];
$car_id = iconv("UTF-8", "CP1251", $car_id);
$sdt = $_GET['sdt'];
$podt = $_GET['podt'];
$shift = $_GET['smena'];
$allday = $_GET['allday'];
$topl = $_GET['topl'];
$tmpr = $_GET['tmpr'];
$prev = $_GET['prev'];
$prev_skor = $_GET['prev_skor'];
$trtype = $_GET['trtype'];

if ($shift !== ''){
	$qshift = "AND (SHIFT = '".$shift."')";
}else{
	$qshift = "";
}

$sgod = substr($sdt, 6,4);
$smes = substr($sdt, 3,2);
$sch = substr($sdt, 0,2);
$sour = substr($sdt, 11,2);
$smin = substr($sdt, 14,2);
$pogod = substr($podt, 6,4);
$pomes = substr($podt, 3,2);
$poch = substr($podt, 0,2);
$poour = substr($podt, 11,2);
$pomin = substr($podt, 14,2);

$sdate = $sgod.$smes.$sch;
$podate = $pogod.$pomes.$poch;

$sdate_f = $sgod."-".$smes."-".$sch;
$podate_f = $pogod."-".$pomes."-".$poch;

//////////////////////////////////////////////////// ЗАПРОСЫ ///////////////////////////////////////////////////////////////

// Если выбран 1 день.
if (($sgod == $pogod)&&($smes == $pomes)&&($sch == $poch)){
	if ($allday == 0){ 	// Запросы для промежутка времени

		$sql_query = "SELECT * FROM BD_GBR WHERE ANUM = '".$car_id."' AND DDATE = '".$sdate_f."' AND TTIME >= '".$sour.":".$smin.":00' AND TTIME <= '".$poour.":".$pomin.":59' ORDER BY DTSTAMP";
		// Запрос по расходу топлива
		$sql_q_topl = "SELECT FUEL, DTSTAMP FROM BD_GBR WHERE ANUM = '".$car_id."' AND DDATE = '".$sdate_f."' AND TTIME >= '".$sour.":".$smin.":00' AND TTIME <= '".$poour.":".$pomin.":59' ORDER BY DTSTAMP";

		// Запрос для превышений скорости
		$prev_query = "SELECT LATT, LONGT, DDATE, TTIME, VEL, TMPR FROM BD_GBR WHERE ANUM = '".$car_id."' AND DDATE = '".$sdate_f."' AND TTIME >= '".$sour.":".$smin.":00' AND TTIME <= '".$poour.":".$pomin.":00' AND VEL > ".$prev_skor." ORDER BY DTSTAMP";

		// Запрос для планируемого трека
		$sql_pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sdate."') AND (DT >= '".$smes."/".$sch."/".$sgod." ".$sour.":".$smin.":00') AND (DT <= '".$smes."/".$sch."/".$sgod." ".$poour.":".$pomin.":59') ORDER BY DT";

		// Запрос для планируемых стоянок
		$sql_vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') AND (DT >= '".$smes."/".$sch."/".$sgod." ".$sour.":".$smin.":00') AND (DT <= '".$smes."/".$sch."/".$sgod." ".$poour.":".$pomin.":59') ORDER BY DT";


	}else{	// запросы для полного дня

		$sql_query = "SELECT * FROM BD_GBR WHERE ANUM = '".$car_id."' AND DDATE = '".$sdate_f."' ORDER BY DTSTAMP";

		// Запрос по расходу топлива
		$sql_q_topl = "SELECT FUEL, DTSTAMP FROM BD_GBR WHERE ANUM = '".$car_id."' AND DDATE = '".$sdate_f."' ORDER BY DTSTAMP";

		// Запрос для превышений скорости
		$prev_query = "SELECT LATT, LONGT, DDATE, TTIME, VEL, TMPR FROM BD_GBR WHERE ANUM = '".$car_id."' AND DDATE = '".$sdate_f."' AND VEL > ".$prev_skor." ORDER BY DTSTAMP";

		// Запрос для планируемого трека
		$sql_pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sdate."') ORDER BY DT";

		// Запрос для планируемых стоянок
		$sql_vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') ORDER BY DT";
	}
} // Конец запросов если выбран 1 день.
else{  // Запрос для трека для 2 и более дней
	if ($allday == 0){	// Запросы для промежутка времени

		$fq = "(DDATE = '".$sdate."') AND (DTSTAMP >= '".$sgod."-".$smes."-".$sch." ".$sour.":".$smin.":00')";
		$fq_sql = "(DDATE = '".$sdate_f."') AND (DTSTAMP >= '".$sdate_f." ".$sour.":".$smin.":00')";

		$i = 0;
		while ($sdate !== $podate){
			$i++;
			$sdate = strftime('%Y%m%d',mktime(0,0,0,$smes,$sch+$i,$sgod,-1));
			$sdate_f = strftime('%Y-%m-%d', mktime(0, 0, 0, $smes, $sch + $i, $sgod, -1));
			if ($sdate == $podate){
				$eq = "(DDATE = '".$sdate."') AND (DTSTAMP <= '".$pogod."-".$pomes."-".$poch." ".$poour.":".$pomin.":59')";
				$eq_sql = "(DDATE = '".$sdate_f."') AND (DTSTAMP <= '".$podate_f." ".$poour.":".$pomin.":59')";
			}
			else{
				$mq .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate."')";
				$mq_sql .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."')";
				$mqt .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."') AND (TYPE_DATA = 34 OR TYPE_DATA = 89)"; // для запроса по топливу
			}
		}
		// Запрос для фактического маршрута
		$sql_query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND ".$fq_sql.$mq_sql." OR (ANUM = '".$car_id."') AND ".$eq_sql." ORDER BY DTSTAMP";
		// Запрос по расходу топлива
		$sql_q_topl = "SELECT FUEL, DTSTAMP FROM BD_GBR WHERE (ANUM = '".$car_id."') AND ".$fq_sql.$mqt." OR (ANUM = '".$car_id."') AND ".$eq_sql." ORDER BY DTSTAMP";
		// Запрос для превышений скорости
		$prev_query = "SELECT LATT, LONGT, DDATE, TTIME, VEL, TMPR FROM BD_GBR WHERE ((ANUM = '".$car_id."') AND (VEL > ".$prev_skor.") AND ".$fq_sql.$mq_sql.") OR ((ANUM = '".$car_id."') AND (VEL > ".$prev_skor.") AND ".$eq_sql.") ORDER BY DTSTAMP";
		// Запрос для планируемого трека
		$sql_pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sdate."') ORDER BY DT";
		// Запрос для планируемых стоянок
		$sql_vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') ORDER BY DT";

	}else{ // запросы для полного дня
		$fq = "(DDATE = '".$sdate."')";
		$fq_sql = "(DDATE = '".$sdate_f."')";

		$i = 0;
		while ($sdate !== $podate){
			$i++;
			$sdate = strftime('%Y%m%d',mktime(0,0,0,$smes,$sch+$i,$sgod,-1));
			$sdate_f = strftime('%Y-%m-%d', mktime(0, 0, 0, $smes, $sch + $i, $sgod, -1));
			$mq .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate."')";
			$mq_sql .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."')";
			$mq_prev .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."') AND (VEL > ".$prev_skor.")";	// для запроса по превышению скорости
			$mqt .= " OR (ANUM = '".$car_id."') AND (DDATE = '".$sdate_f."') AND (TYPE_DATA = 34 OR TYPE_DATA = 89)"; // для запроса по топливу
		}
		// Запрос для фактического маршрута
		$sql_query = "SELECT * FROM BD_GBR WHERE (ANUM = '".$car_id."') AND ".$fq_sql.$mq_sql." ORDER BY DTSTAMP";
		// Запрос по расходу топлива
		$sql_q_topl = "SELECT FUEL, DTSTAMP FROM BD_GBR WHERE (ANUM = '".$car_id."') AND ".$fq_sql.$mqt." ORDER BY DTSTAMP";
		// Запрос для превышений скорости
		$prev_query = "SELECT LATT, LONGT, DDATE, TTIME, VEL, TMPR FROM BD_GBR WHERE ANUM = '".$car_id."' AND VEL > ".$prev_skor." AND ".$fq_sql.$mq_prev." ORDER BY DTSTAMP";
		// Запрос для планируемого трека
		$sql_pquery = "SELECT ANUM, LAT, LON, DT, DTBEG FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (DTBEG = '".$sdate."')ORDER BY DT";
		// Запрос для планируемых стоянок
		$sql_vpquery = "SELECT LAT, LON, DT, PTYPE, ADDRESS FROM BD_PLAN WHERE (ANUM = '".$car_id."') ".$qshift." AND (PTYPE BETWEEN 1 AND 2) AND (DTBEG = '".$sdate."') ORDER BY DT";
	}
} // Конец запросов если выбрано несколько дней.

// Запросы для доп информации
if ($shift > ''){
	$add_shift = " AND BD_ROUTE.SHIFT = '".$shift."'";
}else{
	$add_shift = "";
}
// Запрос SQL
$q_add = "SELECT * FROM BD_ROUTE, SP_OWNER WHERE BD_ROUTE.ANUM = '".$car_id."' $add_shift AND BD_ROUTE.DTBEG = '".$sgod.$smes.$sch."' AND BD_ROUTE.OWNER = SP_OWNER.OWNER";

//////////////////////////////////////////////////// КОНЕЦ ЗАПРОССОВ ///////////////////////////////////////////////////////////////

//////////////////////////////////////////////////// ТРЕК ФАКТИЧЕСКИЙ /////////////////////////////////////////////////////////////////
function timeformat($min){
	if ($min < 60){
		$timeinmove = $min." мин";
	}else{
		$oursinmove = floor($min/60);
		$ostminmove = $min - $oursinmove*60;
		$timeinmove = $oursinmove."ч ".$ostminmove."мин";
	}
	return $timeinmove;
}

function distformat($km){
	if ($km < 1){
		$dist = $km * 1000;
		$dist = $dist." м";
	}else{
		$distkm = floor($km);
		$distm = ($km - $distkm) * 1000;
		$dist = $distkm." км ".$distm." м";
	}
	return $dist;
}
$ar_prev = [];
// Если выбран фактический трек или фактический и плановый
if (($trtype == 0)||($trtype == 2)){

	// Доп. параметры
	$res_dop = $conn->query($q_add);

	$dop_data = '';
	$dop_all = '';
	$detail = "<table class=\"table table-striped\"><tbody>";
	while($data_dop = $res_dop->fetch( PDO::FETCH_ASSOC )){
		$dop_data = trim($data_dop["NAME"]);
		$dop_tinmove = $data_dop["TINMOVE"];
		$dop_tinstop = $data_dop["TINSTOP"];
		$dop_begin = $data_dop["DTFACTB"];
		$prib_progn = $data_dop["FORECDT"];
		$dop_adr = $data_dop["ADDR"];
		$dop_ves = $data_dop["VB"];
		$dop_skor = $data_dop["VEL"];
		$dop_dist = $data_dop["NKM"];
		$dop_smena = $data_dop["SHIFT"];
		$dop_all .= "<div class=\"row\"><div class=\"col-12\"><b>$dop_smena смена</b></div></div>";
	if ($dop_tinmove > ''){
		$timeinmove = timeformat($dop_tinmove);
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">В движении:</div><div class=\"col-9\">$timeinmove</div></div>";
	}
	if ($dop_tinstop > ''){
		$timeinstop = timeformat($dop_tinstop);
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">Время стоянок:</div><div class=\"col-9\">$timeinstop</div></div>";
	}
	if ($dop_begin > ''){
		$date = date_format(date_create($dop_begin), 'd.m.Y H:i');
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">Приб на загрузку:</div><div class=\"col-9\">$date</div></div>";
	}
	if ($prib_progn > ''){
		$date_progn = date_format(date_create($prib_progn), 'd.m.Y H:i');
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">Прогноз прибытия:</div><div class=\"col-9\">$date_progn</div></div>";
	}
	if ($dop_adr > ''){
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">Адрес:</div><div class=\"col-9\"><div class=\"pole-adr\">$dop_adr</div></div></div>";
	}
	if ($dop_ves > ''){
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">Тек тоннаж:</div><div class=\"col-9\">$dop_ves кг</div></div>";
	}
	if ($dop_skor > ''){
		$cur_kor = intval($dop_skor);
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">Скорость:</div><div class=\"col-9\">$cur_kor км/ч</div></div>";
	}
	if ($dop_dist > ''){
		$cur_dist = intval($dop_dist);
		$dop_all .= "<div class=\"row\"><div class=\"col-3\">Дист после загр:</div><div class=\"col-9\">$dop_dist км</div></div>";
	}
	$q_detail = "SELECT * FROM BD_FACT WHERE ANUM = '".$car_id."' AND DTBEG = '".$sgod.$smes.$sch."' AND SHIFT = '".$dop_smena."' ORDER BY DTFACTB";
	$res_detail = $conn->query($q_detail);

	$i = 0;
	$detail .= "<tr><th colspan=\"3\" class=\"text-center\">$dop_smena смена</th></tr>";
	while($data_det = $res_detail->fetch( PDO::FETCH_ASSOC )){
		if ($i > 0){
			$timedur = timeformat($data_det["DURATION"]);
			$det_adr = iconv("CP1251","UTF-8", $data_det["ADDR"]);
			$det_lat = $data_det["LAT"];
			$det_lon = $data_det["LON"];
			$det_dateb = date_format(date_create($data_det["DTFACTB"]), 'd.m H:i');
			$det_datee = date_format(date_create($data_det["DTFACTE"]), 'd.m H:i');
			$det_skor = floor($data_det["AVGSPEED"]);
			$det_dist = distformat($data_det["NKM"]);
			if($data_det["AVGSPEED"] == 0){
				$detail .= "<tr><td>$timedur</td><td width=\"80px\">$det_dateb<br>-<br>$det_datee</td><td><a href=\"#\" onClick=\"gotosub_wm($det_lat,$det_lon)\">$det_adr</a></td></tr>";
			}else{
				$detail .= "<tr><td>$timedur</td><td>$det_skor км/ч</td><td>$det_dist</td></tr>";	
			}
		}
		$i++;
	}
	}
	$detail .= "</tbody></table>";
	if (!isset($data_det)) $detail = '';

	$dop_all .= "<div class=\"row\">
					<div class=\"col-3\">Общее расстояние:</div>
					<div id=\"insdist-$car_id\" class=\"col-9\"></div>
				</div>
				<div class=\"row\">
					<div class=\"col-3\">Макс скорость:</div>
					<div id=\"insskor-$car_id\" class=\"col-9\"></div>
				</div>$detail";

	$count = 0;
	$tcount = 0;
	$maxskor = 0;
	$mintemp = 0;
	$maxtemp = 0;
	$ar_coord = array();

	if (count($ar_coord) == 0){
		$resultid = $conn->query($sql_query);

		while($data_rid = $resultid->fetch( PDO::FETCH_ASSOC )){
			$latt = $data_rid["LATT"];
			$longt = $data_rid["LONGT"];
			if ($latt > 40 && $latt < 65 && $longt > 5 && $longt < 190){
				$ddate = $data_rid["DDATE"];
				$ttime = $data_rid["TTIME"];
				/* Обработка времени */
				$vrem4skor = strtotime($data_rid["DTSTAMP"]);
				($count == 0) ?	$start_dt = $data_rid["DTSTAMP"] : '';
				$end_dt = $data_rid["DTSTAMP"];
				/* Конец обработки времени */
				$dt_time = substr($ddate,8,2).".".substr($ddate,5,2).".".substr($ddate,0,4)." ".substr($ttime,0,5);
				$skor = $data_rid["VEL"];
				$tmpr = $data_rid["TMPR"];
				// $topl = $data_rid["FUEL"];
	
				($skor > $maxskor) ? $maxskor = intval($skor) : '';	//	макс. скорость
	
				$ar_coord[$count]['lat'] = $latt;
				$ar_coord[$count]['lon'] = $longt;
				$ar_coord[$count]['vremya'] = $vrem4skor * 1000;
				$ar_coord[$count]['skor'] = intval($skor);
				$ar_coord[$count]['tmpr'] = round($tmpr, 1);
				// $ar_coord[$count]['topl'] = $topl;
				$ar_coord[$count]['text'] = "<b>$car_id</b> - фактический маршрут<br/><b>дата и время:</b> $dt_time<br /><b>скорость:</b> $skor км/ч<br />";
				if ($count == 0){
					$first_lat = $latt;
					$first_lon = $longt;
					$first_text = "<b>$car_id</b> - начало фактического маршрута<br/><b>дата и время:</b> $dt_time<br /><b>скорость:</b> $skor км/ч";
				}
				if ($trtype == 2){
				if ($count == 0){
					$fmaxlat = $latt;
					$fminlat = $latt;
					$fmaxlon = $longt;
					$fminlon = $longt;
				}else{
					if ($latt > $fmaxlat) $fmaxlat = $latt;
					if ($latt < $fminlat) $fminlat = $latt; 
					if ($longt > $fmaxlon) $fmaxlon = $longt;
					if ($longt < $fminlon) $fminlon = $longt;	
				}
				}
				$count++;
			}
		}
	}
	$ar_topl = array();
	if ($topl == 1) $res_topl = $conn->query($sql_q_topl);
	if (isset($res_topl)){
		while($data_tpl = $res_topl->fetch( PDO::FETCH_ASSOC )){
			$ar_topl[$tcount]['yroven'] = $data_tpl["FUEL"];
			$vrem = strtotime($data_tpl["DTSTAMP"]);
			$ar_topl[$tcount]['vremya'] = $vrem * 1000;

			$tcount++;
		}

		if (count($ar_topl) > 0){
			// Сглаживание графика топлива
			$srez = 60; // Широта охвата точек для регулировки сглаживания
			foreach ($ar_topl as $key => $val){
				$tempArr[] = $val['yroven'];
			}
		
			for ($i = 0; $i < count($tempArr); $i++) {
				$start = $i - round($srez/2);
				$slSrez = $start + $srez;
				if ($slSrez > $srez) $slSrez = $srez;
				if ($start < 0) $start = 0;
				$slice = array();
				$slice = array_slice($tempArr, $start, $slSrez);
				$res = array_sum($slice) / count($slice);
				$ar_topl[$i]['s_yroven'] = round($res, 1);
			}
		}
	}

//	$interval = date_diff($start_dt, $end_dt);
	$last_lat = $latt;
	$last_lon = $longt;
	$last_text = "<b>$car_id</b> - конец фактического маршрута<br/><b>дата и время:</b> $dt_time<br /><b>скорость:</b> $skor км/ч";
	// Превышения скорости
	if ($prev == 1){
		$resultTpl = $conn->query($prev_query);
		$i_prev = 0;
		while ($rtpl = $resultTpl->fetch( PDO::FETCH_ASSOC )){
			$ddate = $rtpl["DDATE"];
			$ttime = $rtpl["TTIME"];
			$prevSkor =  round($rtpl["VEL"], 1);
			$ar_prev[$i_prev]['lat'] = $rtpl["LATT"];
			$ar_prev[$i_prev]['lon'] = $rtpl["LONGT"];
			$ar_prev[$i_prev]['text'] = "<b>Превышение скорости ".$car_id."</b><br/>Дата и время: ".substr($ddate, 8, 2).".".substr($ddate, 5, 2).".".substr($ddate, 0, 4)." ".$ttime."<br/>Скорость: ".$prevSkor." км/ч";
			$i_prev++;
		}
	}
}
//////////////////////////////////////////////////// КОНЕЦ ТРЕК ФАКТИЧЕСКИЙ /////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////// ТРЕК ПЛАНОВЫЙ /////////////////////////////////////////////////////////////////

if (($trtype == 1)||($trtype == 2)){

// Планируемый трек

$presultid = $conn->query($sql_pquery);

$pi = 0;
while($data_prid = $presultid->fetch( PDO::FETCH_ASSOC )){
	$platt = $data_prid["LAT"];
	$plongt = $data_prid["LON"];
	$pddate = $data_prid["DT"];
	$ar_pcoord[$pi]['lat'] = $platt;
	$ar_pcoord[$pi]['lon'] = $plongt;
	$ar_pcoord[$pi]['date'] = substr($pddate,8,2).".".substr($pddate,5,2).".".substr($pddate,0,4)." ".substr($pddate,11,5);

	if ($trtype == 2){
		if ($pi == 0){
		$pmaxlat = $platt;
		$pminlat = $platt;
		$pmaxlon = $plongt;
		$pminlon = $plongt;
	}else{
		if ($platt > $pmaxlat) $pmaxlat = $platt;
		if ($platt < $pminlat) $pminlat = $platt; 
		if ($plongt > $pmaxlon) $pmaxlon = $plongt;
		if ($plongt < $pminlon) $pminlon = $plongt;
	}
	}
	$pi++;
}


// наполнение маркерами план. стоянок
$vpresultid = $conn->query($sql_vpquery);

$vpi = 0;
while($data_vprid = $vpresultid->fetch( PDO::FETCH_ASSOC )){
	$vpi++;
	$vplatt = $data_vprid["LAT"];
	$vplongt = $data_vprid["LON"];
	$vpddate = $data_vprid["DT"];
	$vpddate = substr($vpddate,0,19);
	$vptype = $data_vprid["PTYPE"];
	$adr = $data_vprid["ADDRESS"];
	$adr = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $adr);
	$adr = trim($adr);
	$adr = htmlspecialchars($adr);
	if ($vptype == 1){ // если погрузка
		$pstop_txt = "<b>Погрузка $car_id</b><br/>Время: ".substr($vpddate,8,2).".".substr($vpddate,5,2).".".substr($vpddate,0,4)." ".substr($vpddate,11,5)."<br/>Адрес: ";
	}
	else{ // если разгрузка
		$pstop_txt = "<b>Разгрузка $car_id</b><br/>Время: ".substr($vpddate,8,2).".".substr($vpddate,5,2).".".substr($vpddate,0,4)." ".substr($vpddate,11,5)."<br/>Адрес: ";
	}
	$c_pstops = $vpi - 1;
	$ar_pscoord[$c_pstops]['lat'] = $vplatt;
	$ar_pscoord[$c_pstops]['lon'] = $vplongt;
	$ar_pscoord[$c_pstops]['num'] = $vpi;
	$ar_pscoord[$c_pstops]['adr'] = $adr;
	$ar_pscoord[$c_pstops]['text'] = $pstop_txt;
}
}

//////////////////////////////////////////////////// КОНЕЦ ТРЕК ПЛАНОВЫЙ /////////////////////////////////////////////////////////////////

if ($trtype == 0){		// результаты для фактического трека
if ($count > 0){
	$result = array('type'=>0, 'track'=>$ar_coord, 'topl'=>$ar_topl, 'f_lat'=>$first_lat, 'f_lon'=>$first_lon, 'f_txt'=>$first_text, 'l_lat'=>$last_lat, 'l_lon'=>$last_lon, 'l_txt'=>$last_text, 'maxskor'=>$maxskor, 'dop_inf'=>$dop_data, 'dop_all'=>$dop_all, 'prevs'=>$ar_prev);
}
else{
	$result = array('type'=>'error');
}
}

if ($trtype == 1){		// результаты для планового трека
if ($pi > 0){
	$result = array('type'=>1, 'ptrack'=>$ar_pcoord, 'pstops'=>$ar_pscoord);
}
else{
	$result = array('type'=>'error');
}
}

if ($trtype == 2){		// результаты для фактического и планового треков

	if ($fmaxlat >= $pmaxlat){
		$maxlat = $fmaxlat; 
	}else{
		$maxlat = $pmaxlat;
	}
	if ($fmaxlon >= $pmaxlon){
		$maxlon = $fmaxlon; 	
	}else{
		$maxlon = $pmaxlon;
	}
	if ($fminlat <= $pminlat){
		$minlat = $fminlat; 
	}else{
		$minlat = $pminlat;
	}
	if ($fminlon <= $pminlon){
		$minlon = $fminlon; 	
	}else{
		$minlon = $pminlon;
	}
	if (($count > 0)&&($pi > 0)){
		$result = array('type'=>2, 'track'=>$ar_coord, 'topl'=>$ar_topl, 'f_lat'=>$first_lat, 'f_lon'=>$first_lon, 'f_txt'=>$first_text, 'l_lat'=>$last_lat, 'l_lon'=>$last_lon, 'l_txt'=>$last_text, 'ptrack'=>$ar_pcoord, 'pstops'=>$ar_pscoord, 'maxlat'=>$maxlat, 'maxlon'=>$maxlon, 'minlat'=>$minlat, 'minlon'=>$minlon, 'maxskor'=>$maxskor, 'dop_inf'=>$dop_data, 'dop_all'=>$dop_all, 'prevs'=>$ar_prev);
	}
	elseif (($count > 0)&&($pi == 0)){
		$result = array('type'=>0, 'track'=>$ar_coord, 'topl'=>$ar_topl, 'f_lat'=>$first_lat, 'f_lon'=>$first_lon, 'f_txt'=>$first_text, 'l_lat'=>$last_lat, 'l_lon'=>$last_lon, 'l_txt'=>$last_text, 'maxlat'=>$fmaxlat, 'maxlon'=>$fmaxlon, 'minlat'=>$fminlat, 'minlon'=>$fminlon, 'maxskor'=>$maxskor, 'dop_inf'=>$dop_data, 'dop_all'=>$dop_all, 'prevs'=>$ar_prev."<div class=\"h6\">Нет планового маршрута</div>");
	}
	elseif (($count == 0)&&($pi > 0)){
		$result = array('type'=>1, 'ptrack'=>$ar_pcoord, 'pstops'=>$ar_pscoord, 'maxlat'=>$pmaxlat, 'maxlon'=>$pmaxlon, 'minlat'=>$pminlat, 'minlon'=>$pminlon);
	}
	elseif (($count == 0)&&($pi == 0)){
		$result = array('type'=>'error');
	}
}
//////////////////////////////////////////////////////////////
}
elseif($ver == 2){
	$result = array('type'=>3);
}

print json_encode($result);
?>