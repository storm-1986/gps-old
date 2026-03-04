<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "../options.php";

$anum = $_POST['car'];
$dt_begin = $_POST['date_b'];
$stops = $_POST['stops'];

$day = date("d");
$mes = date("m");
$year = date("Y");

$col_shift_q = "SELECT SHIFT FROM BD_PLAN WHERE ANUM = '" . $anum . "' AND DTBEG  = '" . $dt_begin . "' GROUP BY SHIFT";
$col_shift = $conn->query($col_shift_q);		// запрос количества смен
$count_shift = 0;
while($shifts = $col_shift->fetch( PDO::FETCH_ASSOC )){
	$shift = $shifts["SHIFT"];

	$mainq = "SELECT * FROM BD_PLAN WHERE ANUM = '" . $anum . "' AND DTBEG  = '" . $dt_begin . "' AND SHIFT = '" . $shift . "' ORDER BY DT";
	$qpltrack = $conn->query($mainq);		// запрос планового маршрута
	$i = 0;
	while($data_track = $qpltrack->fetch( PDO::FETCH_ASSOC )){
		$platt = $data_track["LAT"];
		$plongt = $data_track["LON"];
		$pddate = $data_track["DT"];
	
		$ar_pcoord[$count_shift][$i]['lat'] = $platt;
		$ar_pcoord[$count_shift][$i]['lon'] = $plongt;
		$ar_pcoord[$count_shift][$i]['date'] = substr($pddate, 8, 2) . "." . substr($pddate, 5, 2) . "." . substr($pddate, 0, 4) . " " . substr($pddate, 11, 5);
		$i++;		
	}
	if ($i > 0){
		$text_qplstops = "SELECT * FROM BD_PLAN WHERE DTBEG  = '" . $dt_begin . "' AND ANUM = '" . $anum . "' AND SHIFT = '" . $shift . "' AND PTYPE <> 5 ORDER BY DT";
		$qplstops = $conn->query($text_qplstops);		// запрос стоянок планового маршрута
		$count = 0;
		while($data_stops = $qplstops->fetch( PDO::FETCH_ASSOC )){
			$db_anum = trim($data_stops["ANUM"]); 
			$slatt = $data_stops["LAT"];
			$slongt = $data_stops["LON"];
			$sddate = $data_stops["DT"];
			$point_type = $data_stops["PTYPE"];
			$smena = $data_stops["SHIFT"];
			$st_adr = $data_stops["ADDRESS"];
			if ($point_type == 1){
				$stop_type = 'Погрузка';
			}else{
				$stop_type = 'Разгрузка';
			}

			$ar_stops[$count_shift][$count]['lat'] = $slatt;
			$ar_stops[$count_shift][$count]['lon'] = $slongt;
			$ar_stops[$count_shift][$count]['stat'] = $stops;
			$ar_stops[$count_shift][$count]['adr'] = "Смена: $smena $st_adr";
			$ar_stops[$count_shift][$count]['text'] = "<b>" . $stop_type . ":</b><br/>" . $st_adr . "<br>Планируемое " . substr($sddate, 8, 2) . "." . substr($sddate, 5, 2) . "." . substr($sddate, 0, 4) . " " . substr($sddate, 11, 8);
			$count++;
		}
	}
	$count_shift++;
}

$last_q = "SELECT TOP 1 * FROM BD_GBR WHERE ANUM = '".$anum."' AND DDATE = '".$year."-".$mes."-".$day."' ORDER BY DTSTAMP DESC";
$qrpos = $conn->query($last_q);		// запрос последних координат
while($data_last = $qrpos->fetch( PDO::FETCH_ASSOC )){
	$rlat = $data_last["LATT"];
	$rlon = $data_last["LONGT"];
	$rdate = $data_last["DDATE"];
	$rtime = $data_last["TTIME"];
	$rspeed = $data_last["VEL"];
}
$rdttime = "<b>".$anum."</b><br/>".substr($rdate, 8, 2).".".substr($rdate, 5, 2).".".substr($rdate, 0, 4)." ".$rtime."<br/>Скорость: ".$rspeed." км/ч";
if ($i > 0){
	$result = array('type'=>'success','tracks'=>$ar_pcoord,'pstops'=>$ar_stops,'r_lat'=>$rlat,'r_lon'=>$rlon,'r_text'=>$rdttime,'query'=>$text_qplstops,'test_q'=>$text_qplstops);
}else{
	$result = array('type'=>'error','msg'=>"Нет планового маршрута");
}
print json_encode($result);
?>