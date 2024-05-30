<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

if(isset($_GET['su_name'])) $user_name = $_GET['su_name'];
if(isset($_POST['su_name'])) $user_name = $_POST['su_name'];
if(isset($_GET['f_gwx'])) $f_gwx = $_GET['f_gwx'];
if(isset($_POST['f_gwx'])) $f_gwx = $_POST['f_gwx'];
if(isset($_GET['su_type'])) $user_type = $_GET['su_type'];
if(isset($_GET['gidmap'])) $city_name = $_GET['gidmap'];
if(isset($_GET['car'])) $car_id = $_GET['car'];
if(isset($_GET['su_own']))	$u_owner = $_GET['su_own'];

if (isset($_GET['sdt'])||isset($_GET['podt'])){
	$sdt = $_GET['sdt'];
	$podt = $_GET['podt'];

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
}
if(isset($_GET['allday'])) $allday = $_GET['allday'];
if(isset($_GET['trtype'])) $trtype = $_GET['trtype'];

if(isset($_GET['slat'])) $slat = $_GET['slat'];
if(isset($_GET['slong'])) $slong = $_GET['slong'];
if(isset($_GET['addr'])) $addr = $_GET['addr'];
if(isset($_POST['addr'])) $addr = $_POST['addr'];
if(isset($_GET['nmnlat'])) $nmnlat = $_GET['nmnlat'];
if(isset($_GET['nmnlon'])) $nmnlon = $_GET['nmnlon'];
if(isset($_GET['nmxlat'])) $nmxlat = $_GET['nmxlat'];
if(isset($_GET['nmxlon'])) $nmxlon = $_GET['nmxlon'];
if(isset($_GET['imh'])) $imh = $_GET['imh'];
if(isset($_GET['imw'])) $imw = $_GET['imw'];
if(isset($_POST['imh'])) $imh = $_POST['imh'];
if(isset($_POST['imw'])) $imw = $_POST['imw'];
if(isset($_POST['region'])) $obl = $_POST['region'];
if(isset($_GET['scale'])){
	$scale = $_GET['scale'];
}
else{
	$scale = 10000;
}

if ($f_gwx == "track"){
	$ftime = $_GET['ftime'];
	$ftmpr = $_GET['ftmpr'];
	$fskor = $_GET['fskor'];
	$fdot = $_GET['fdot'];
	$fstrel = $_GET['fstrel'];
}

$seluser = $conn->query("SELECT OWNER, LCTN FROM SP_USER WHERE LOGIN = '".$user_name."'");

while ($data_user = $seluser->fetch( PDO::FETCH_ASSOC )){
	$resowner = $data_user["OWNER"];	
	$reslctn = $data_user["LCTN"];
}

$str2 = "		<MINTIME type=\"string\">60</MINTIME>\n		<MINPATH type=\"string\">50</MINPATH>\n";

if (($f_gwx == "intrep")||($f_gwx == "repstop")||($f_gwx == "general")||($f_gwx == "repstopimg")||($f_gwx == "repstopll")||($f_gwx == "reptmpr")||($f_gwx == "repfuel")||($f_gwx == "repsteptmpr")){
	if ($allday == 0){
		$str1 = "		<DTBEG type=\"string\">".$sgod.$smes.$sch.$sour.$smin."00</DTBEG>\n		<DTEND type=\"string\">".$pogod.$pomes.$poch.$poour.$pomin."00</DTEND>\n";
	}
	else{
		$str1 = "		<DTBEG type=\"string\">".$sgod.$smes.$sch."000000</DTBEG>\n		<DTEND type=\"string\">".$pogod.$pomes.$poch."235959</DTEND>\n";
	}
}

if ($f_gwx == "repcmp"){
	if ($allday == 0){
		$str1 = "		<DTBEG type=\"string\">".$sgod.$smes.$sch.$sour.$smin."00</DTBEG>\n		<DTBEGPLAN type=\"string\">".$sgod.$smes.$sch."</DTBEGPLAN>\n		<DTEND type=\"string\">".$pogod.$pomes.$poch.$poour.$pomin."00</DTEND>\n";
	}
	else{
		$str1 = "		<DTBEG type=\"string\">".$sgod.$smes.$sch."000000</DTBEG>\n		<DTBEGPLAN type=\"string\">".$sgod.$smes.$sch."</DTBEGPLAN>\n		<DTEND type=\"string\">".$pogod.$pomes.$poch."235959</DTEND>\n";
	}
}

if (($f_gwx == "track")||($f_gwx == "stops")){
	if ($allday == 0){
		$str1 = "		<DTBEG type=\"string\">".$sgod.$smes.$sch.$sour.$smin."00</DTBEG>\n		<DTEND type=\"string\">".$pogod.$pomes.$poch.$poour.$pomin."00</DTEND>\n";
	}
	else{
		$str1 = "		<DTBEG type=\"string\">".$sgod.$smes.$sch."000000</DTBEG>\n		<DTEND type=\"string\">".$pogod.$pomes.$poch."235959</DTEND>\n";
	}
}

switch ($f_gwx){

	case "lastcar":
		$zrep = "RepLastCar";
		$qtype = "GetReport";
		$frnap = "		<Cars>\n".$str3."		</Cars>\n";		
		break;
	case "intrep":
		$zrep = "RepInterval";
		$qtype = "GetReport";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n		<PRIORITY type=\"string\">".$user_type."</PRIORITY>\n".$str1.$str2;
		break;
	case "repstop":
		$zrep = "RepStop";
		$qtype = "GetReport";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1.$str2;
		break;
	case "general":
		$zrep = "RepGeneral";
		$qtype = "GetReport";
		$frnap = "		<Cars>\n".$str3."		</Cars>\n".$str1.$str2;
		break;
	case "repstopimg":
		$zrep = "RepStopImg";
		$qtype = "GetReport";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1.$str2;
		break;
	case "repstopll":
		$zrep = "RepStopLatLon";
		$qtype = "GetReport";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1.$str2;
		break;
	case "repcmp":
		$zrep = "RepCmp";
		$qtype = "GetReport";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1.$str2;
		break;
	case "repdev":
		$zrep = "RepDev";
		$qtype = "GetReport";
		$frnap = "		<OWNER>".$u_owner."</OWNER>\n";
		break;
	case "stops":
		$zrep = "GetRoutePoints";
		$qtype = "GWNAV";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1.$str2;
		break;
	case "reptmpr":
		$zrep = "RepTmpr";
		$qtype = "SetService";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1."		<TMPRMIN>2</TMPRMIN>\n		<TMPRMAX>6</TMPRMAX>\n";
		break;
	case "repfuel":
		$zrep = "RepFuel";
		$qtype = "SetService";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1."		<DRAINLIMIT>3</DRAINLIMIT>\n";
		break;
	case "repsteptmpr":
		$zrep = "RepStepTmpr";
		$qtype = "SetService";
		$frnap = "		<ANUM type=\"string\">".$car_id."</ANUM>\n".$str1."		<STEP>15</STEP>\n";
		break;
}

$msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<body login=\"".$user_name."\">\n<".$qtype.">\n	<".$zrep.">\n".$frnap."	</".$zrep.">\n</".$qtype.">\n</body>\r\n.\r\n";

include "sock.php";

if (isset ($out)){
	$patterns = [
		'/(, Республика)* Беларусь/',
		'/область/',
		'/район/',
		'/сельский Совет/',
		'/улица/',
		'/проспект/',
		'/(, )*\d{6},*/',
	];
	$replacements = [
		'',
		'обл.',
		'р-н',
		'сел. cов.',
		'ул.',
		'пр-т',
		'',
	];
	preg_match_all("/<Error>(.+)<\/Error>/sUi", $out, $errors);
	if (count($errors[1]) == 0){
		if ($f_gwx == "stops"){
			$out_arr = array();
			$out_arr1 = array();
			$out_arr2 = array();
			$out_arr3 = array();
			$out_arr4 = array();
			$out_arr5 = array();
			$out_arr6 = array();
			$out_arr7 = array();
			$si = 0;
			preg_match_all("/<Row>(.+)<\/Row>/sUi", $out, $out_arr);
			foreach ($out_arr[1] as $k => $v){
				// echo "<br>Key ".$k.": ".$v."<br><br>";
				preg_match("/<DTStop>(.+)<\/DTStop>/", $v, $out_arr1);
				if (count($out_arr1) > 0){
					$dtstop = substr($out_arr1[1],6,2).".".substr($out_arr1[1],4,2).".".substr($out_arr1[1],0,4)." ".substr($out_arr1[1],8,2).":".substr($out_arr1[1],10,2).":".substr($out_arr1[1],12,2);
				}
				else{
					$dtstop = '-';
				}
				preg_match("/<DTEnd>(.+)<\/DTEnd>/", $v, $out_arr2);
				if (count($out_arr2) > 0){
					$dtend = substr($out_arr2[1],6,2).".".substr($out_arr2[1],4,2).".".substr($out_arr2[1],0,4)." ".substr($out_arr2[1],8,2).":".substr($out_arr2[1],10,2).":".substr($out_arr2[1],12,2);
				}
				else{
					$dtend = '-';
				}
				preg_match("/<Lat>(.+)<\/Lat>/", $v, $out_arr3);
				preg_match("/<Lon>(.+)<\/Lon>/", $v, $out_arr4);
				preg_match("/<TmprStop>(.+)<\/TmprStop>/", $v, $out_arr5);
				if (count($out_arr5) > 0){
					if ($out_arr5[1] < -8 || $out_arr5[1] > 40){
						$mpr1 = '-';
					}else{
						$mpr1 = $out_arr5[1];
					}
				}
				preg_match("/<TmprEnd>(.+)<\/TmprEnd>/", $v, $out_arr6);
				if (count($out_arr6) > 0){
					if ($out_arr6[1] < -8 || $out_arr6[1] > 40){
						$mpr2 = '-';
					}else{
						$mpr2 = $out_arr6[1];
					}
				}
				preg_match("/<Address>(.+)<\/Address>/", $v, $out_arr7);
				if (count($out_arr7) > 0){
					$stopAddr = $out_arr7[1];
				}else{
					$stopAddr = '';
				}
				$ar_fstops[$si]['lat'] = $out_arr3[1];
				$ar_fstops[$si]['lon'] = $out_arr4[1];
				$ar_fstops[$si]['text'] = "<div><b>Стоянка $car_id</b></div><div class='row'><div class='col-5 pl-0 pr-0'>Адрес</div><div class='col-7 pl-0 pr-0'>$stopAddr</div></div><div class='row'><div class='col-5 pl-0 pr-0'>Начало</div><div class='col-7 pl-0 pr-0'>$dtstop</div></div><div class='row'><div class='col-5 pl-0 pr-0'>Конец</div><div class='col-7 pl-0 pr-0'>$dtend</div></div>";
				$si++;
			}
		$result = array('type'=>'success', 'fstops'=>$ar_fstops);
		}
		elseif($f_gwx == "reptmpr"){
			preg_match("/<ANUM>(.+)<\/ANUM>/", $out, $outAnum);
			preg_match("/<DTBEG>(.+)<\/DTBEG>/sUi", $out, $outDtbeg);
			preg_match("/<DTEND>(.+)<\/DTEND>/sUi", $out, $outDtend);
			preg_match("/<TMPRMIN>(.+)<\/TMPRMIN>/sUi", $out, $outTmprmin);
			preg_match("/<TMPRMAX>(.+)<\/TMPRMAX>/sUi", $out, $outTmprmax);
			preg_match_all("/<ROW>(.+)<\/ROW>/sUi", $out, $outAlarms);
			$alarms = array();
			if (count($outAlarms[1]) > 0) {
				$i = 0;
				foreach ($outAlarms[1] as $key => $val){
					preg_match("/<DTBEG>(.+)<\/DTBEG>/sUi", $val, $alarmDtbeg);
					preg_match("/<DTEND>(.+)<\/DTEND>/sUi", $val, $alarmDtend);
					preg_match("/<TMPRBEG>(.+)<\/TMPRBEG>/sUi", $val, $alarmTbeg);
					preg_match("/<TMPREND>(.+)<\/TMPREND>/sUi", $val, $alarmTend);
					preg_match("/<TMPRMIN>(.+)<\/TMPRMIN>/sUi", $val, $alarmTmin);
					preg_match("/<TMPRMAX>(.+)<\/TMPRMAX>/sUi", $val, $alarmTmax);
					preg_match("/<LAT>(.+)<\/LAT>/sUi", $val, $alarmLat);
					preg_match("/<LON>(.+)<\/LON>/sUi", $val, $alarmLon);
					$alarms[$i]['DTBEG'] = substr($alarmDtbeg[1], 6, 2).'.'.substr($alarmDtbeg[1], 4, 2).'.'.substr($alarmDtbeg[1], 0, 4).' '.substr($alarmDtbeg[1], 8, 2).':'.substr($alarmDtbeg[1], 10, 2).':'.substr($alarmDtbeg[1], 12, 2);
					$alarms[$i]['DTEND'] = substr($alarmDtend[1], 6, 2).'.'.substr($alarmDtend[1], 4, 2).'.'.substr($alarmDtend[1], 0, 4).' '.substr($alarmDtend[1], 8, 2).':'.substr($alarmDtend[1], 10, 2).':'.substr($alarmDtend[1], 12, 2);
					$alarms[$i]['TMPRBEG'] = round($alarmTbeg[1], 1);
					$alarms[$i]['TMPREND'] = round($alarmTend[1], 1);
					$alarms[$i]['TMPRMIN'] = round($alarmTmin[1], 1);
					$alarms[$i]['TMPRMAX'] = round($alarmTmax[1], 1);
					$alarms[$i]['LAT'] = $alarmLat[1];
					$alarms[$i]['LON'] = $alarmLon[1];
					$i++;
				}
			}
			$result = array(
				'ANUM' => $outAnum[1],
				'DTBEG' => substr($outDtbeg[1], 6, 2).'.'.substr($outDtbeg[1], 4, 2).'.'.substr($outDtbeg[1], 0, 4).' '.substr($outDtbeg[1], 8, 2).':'.substr($outDtbeg[1], 10, 2),
				'DTEND' => substr($outDtend[1], 6, 2).'.'.substr($outDtend[1], 4, 2).'.'.substr($outDtend[1], 0, 4).' '.substr($outDtend[1], 8, 2).':'.substr($outDtend[1], 10, 2),
				'TMPRMIN' => round($outTmprmin[1], 1),
				'TMPRMAX' => round($outTmprmax[1], 1),
				'ALARMS' => $alarms,
			);
		}
		elseif($f_gwx == "repsteptmpr"){
			preg_match("/<ANUM>(.+)<\/ANUM>/", $out, $outAnum);
			preg_match("/<DTBEG>(.+)<\/DTBEG>/sUi", $out, $outDtbeg);
			preg_match("/<DTEND>(.+)<\/DTEND>/sUi", $out, $outDtend);
			preg_match_all("/<ROW>(.+)<\/ROW>/sUi", $out, $outValues);
			$values = array();
			if (count($outValues[1]) > 0) {
				$i = 0;
				foreach ($outValues[1] as $key => $val){
					preg_match("/<DT>(.+)<\/DT>/sUi", $val, $valueDt);
					preg_match("/<TMPR>(.+)<\/TMPR>/sUi", $val, $valueTmpr);
					$values[$i]['DT'] = substr($valueDt[1], 6, 2).'.'.substr($valueDt[1], 4, 2).'.'.substr($valueDt[1], 0, 4).' '.substr($valueDt[1], 8, 2).':'.substr($valueDt[1], 10, 2).':'.substr($valueDt[1], 12, 2);
					$values[$i]['TMPR'] = round($valueTmpr[1], 1);
					$i++;
				}
			}
			$result = array(
				'ANUM' => $outAnum[1],
				'DTBEG' => substr($outDtbeg[1], 6, 2).'.'.substr($outDtbeg[1], 4, 2).'.'.substr($outDtbeg[1], 0, 4).' '.substr($outDtbeg[1], 8, 2).':'.substr($outDtbeg[1], 10, 2),
				'DTEND' => substr($outDtend[1], 6, 2).'.'.substr($outDtend[1], 4, 2).'.'.substr($outDtend[1], 0, 4).' '.substr($outDtend[1], 8, 2).':'.substr($outDtend[1], 10, 2),
				'VALUES' => $values,
			);
		}
		elseif($f_gwx == "repfuel"){
			preg_match("/<ANUM>(.+)<\/ANUM>/", $out, $outAnum);
			preg_match("/<DTBEG>(.+)<\/DTBEG>/sUi", $out, $outDtbeg);
			preg_match("/<DTEND>(.+)<\/DTEND>/sUi", $out, $outDtend);
			preg_match("/<FUEL>(.+)<\/FUEL>/sUi", $out, $outFuel);
			preg_match("/<FUELMOVE>(.+)<\/FUELMOVE>/sUi", $out, $outFuelMove);
			preg_match("/<FUELSTOP>(.+)<\/FUELSTOP>/sUi", $out, $outFuelStop);
			preg_match("/<FUELBEG>(.+)<\/FUELBEG>/sUi", $out, $outFuelBeg);
			preg_match("/<FUELEND>(.+)<\/FUELEND>/sUi", $out, $outFuelEnd);
			preg_match("/<LENCAN>(.+)<\/LENCAN>/sUi", $out, $outLenCan);
			preg_match("/<LENGPS>(.+)<\/LENGPS>/sUi", $out, $outLenGps);
			preg_match("/<LENCANBEG>(.+)<\/LENCANBEG>/sUi", $out, $outLenCanBeg);
			preg_match("/<LENCANEND>(.+)<\/LENCANEND>/sUi", $out, $outLenCanEnd);
			preg_match("/<REFILL>(.+)<\/REFILL>/sUi", $out, $outRefill);
			preg_match("/<REFILLCNT>(.+)<\/REFILLCNT>/sUi", $out, $outRefillCnt);
			preg_match("/<DRAINCNT>(.+)<\/DRAINCNT>/sUi", $out, $outDrainCnt);
			preg_match("/<STOPCNT>(.+)<\/STOPCNT>/sUi", $out, $outStopCnt);
			preg_match("/<MOVETIME>(.+)<\/MOVETIME>/sUi", $out, $outMovetime);
			preg_match("/<AVGSPEED>(.+)<\/AVGSPEED>/sUi", $out, $outAvgSpeed);
			preg_match_all("/<ROW>(.+)<\/ROW>/sUi", $out, $outAlarms);
			$alarms = array();
			if (count($outAlarms[1]) > 0) {
				$i = 0;
				foreach ($outAlarms[1] as $key => $val){
					preg_match("/<DT>(.+)<\/DT>/sUi", $val, $alarmDt);
					preg_match("/<BEFORE>(.+)<\/BEFORE>/sUi", $val, $alarmBefore);
					preg_match("/<DRAIN>(.+)<\/DRAIN>/sUi", $val, $alarmDrain);
					preg_match("/<AFTER>(.+)<\/AFTER>/sUi", $val, $alarmAfter);
					preg_match("/<LAT>(.+)<\/LAT>/sUi", $val, $alarmLat);
					preg_match("/<LON>(.+)<\/LON>/sUi", $val, $alarmLon);
					preg_match("/<COMMENT>(.+)<\/COMMENT>/sUi", $val, $alarmComment);
					$alarms[$i]['DT'] = substr($alarmDt[1], 6, 2).'.'.substr($alarmDt[1], 4, 2).'.'.substr($alarmDt[1], 0, 4).' '.substr($alarmDt[1], 8, 2).':'.substr($alarmDt[1], 10, 2).':'.substr($alarmDt[1], 12, 2);
					$alarms[$i]['BEFORE'] = $alarmBefore[1];
					$alarms[$i]['DRAIN'] = $alarmDrain[1];
					$alarms[$i]['AFTER'] = $alarmAfter[1];
					$alarms[$i]['LAT'] = $alarmLat[1];
					$alarms[$i]['LON'] = $alarmLon[1];
					$alarms[$i]['COMMENT'] = $alarmComment[1];
					$i++;
				}
			}
			$result = array(
				'ANUM' => $outAnum[1],
				'DTBEG' => substr($outDtbeg[1], 6, 2).'.'.substr($outDtbeg[1], 4, 2).'.'.substr($outDtbeg[1], 0, 4).' '.substr($outDtbeg[1], 8, 2).':'.substr($outDtbeg[1], 10, 2),
				'DTEND' => substr($outDtend[1], 6, 2).'.'.substr($outDtend[1], 4, 2).'.'.substr($outDtend[1], 0, 4).' '.substr($outDtend[1], 8, 2).':'.substr($outDtend[1], 10, 2),
				'FUEL' => $outFuel[1],
				'FUELMOVE' => $outFuelMove[1],
				'FUELSTOP' => $outFuelStop[1],
				'FUELBEG' => $outFuelBeg[1],
				'FUELEND' => $outFuelEnd[1],
				'LENCAN' => $outLenCan[1],
				'LENGPS' => $outLenGps[1],
				'LENCANBEG' => $outLenCanBeg[1],
				'LENCANEND' => $outLenCanEnd[1],
				'REFILL' => $outRefill[1],
				'REFILLCNT' => $outRefillCnt[1],
				'DRAINCNT' => $outDrainCnt[1],
				'STOPCNT' => $outStopCnt[1],
				'MOVETIME' => $outMovetime[1],
				'AVGSPEED' => $outAvgSpeed[1],
				'ALARMS' => $alarms,
			);
		}
		elseif($f_gwx == "intrep"){
			preg_match("/<ANUM>(.+)<\/ANUM>/", $out, $outAnum);
			preg_match("/<DTBEG>(.+)<\/DTBEG>/sUi", $out, $outDtbeg);
			preg_match("/<FBEG>(.+)<\/FBEG>/sUi", $out, $intFBegin);
			preg_match("/<FEND>(.+)<\/FEND>/sUi", $out, $intFEnd);
			preg_match("/<FT1>(.+)<\/FT1>/sUi", $out, $tmprFBegin);
			preg_match("/<FT2>(.+)<\/FT2>/sUi", $out, $tmprFEnd);
			preg_match("/<FLEN>(.+)<\/FLEN>/sUi", $out, $intFLen);
			preg_match("/<FAVVEL>(.+)<\/FAVVEL>/sUi", $out, $intFAvVel);
			preg_match("/<FMAXVEL>(.+)<\/FMAXVEL>/sUi", $out, $intFMaxVel);
			preg_match("/<FTMOVE>(.+)<\/FTMOVE>/sUi", $out, $intFMove);
			preg_match("/<FTSTOP>(.+)<\/FTSTOP>/sUi", $out, $intFStop);
			preg_match_all("/<ROW>(.+)<\/ROW>/sUi", $out, $outIntervals);
			$intervals = array();
			if (count($outIntervals[1]) > 0) {
				$i = 0;
				foreach ($outIntervals[1] as $key => $val){
					preg_match("/<BEG>(.+)<\/BEG>/sUi", $val, $intBegin);
					preg_match("/<END>(.+)<\/END>/sUi", $val, $intEnd);
					preg_match("/<TBEG>(.+)<\/TBEG>/sUi", $val, $tmprBegin);
					preg_match("/<TEND>(.+)<\/TEND>/sUi", $val, $tmprEnd);
					preg_match("/<LEN>(.+)<\/LEN>/sUi", $val, $intLen);
					preg_match("/<AVVEL>(.+)<\/AVVEL>/sUi", $val, $intAvVel);
					preg_match("/<MAXVEL>(.+)<\/MAXVEL>/sUi", $val, $intMaxVel);
					preg_match("/<MOVETIME>(.+)<\/MOVETIME>/sUi", $val, $intMove);
					preg_match("/<STOPTIME>(.+)<\/STOPTIME>/sUi", $val, $intStop);
					preg_match("/<LATB>(.+)<\/LATB>/sUi", $val, $intLatB);
					preg_match("/<LONB>(.+)<\/LONB>/sUi", $val, $intLonB);
					preg_match("/<LATS>(.+)<\/LATS>/sUi", $val, $intLatS);
					preg_match("/<LONS>(.+)<\/LONS>/sUi", $val, $intLonS);
					if (substr($intBegin[1], 0, 5) == '_____'){
						$intervals[$i]['DT'] = str_replace(['_', ' '], '', $intBegin[1]);
					}else{
						$posBegin = strpos($intBegin[1], ' - ');
						$timeBegin = substr($intBegin[1], 0, $posBegin);
						$adrBegin = substr($intBegin[1], $posBegin + 3);
						$adrBegin = preg_replace($patterns, $replacements, $adrBegin);
						$posEnd = strpos($intEnd[1], ' - ');
						$timeEnd = substr($intEnd[1], 0, $posEnd);
						$adrEnd = substr($intEnd[1], $posEnd + 3);
						$adrEnd = preg_replace($patterns, $replacements, $adrEnd);

						$intervals[$i]['INTERVAL'] = $timeBegin . '<br>' . $timeEnd;
						$intervals[$i]['BEGADR'] = $adrBegin;
						$intervals[$i]['ENDADR'] = $adrEnd;
						$intervals[$i]['TBEG'] = round($tmprBegin[1], 1);
						$intervals[$i]['TEND'] = round($tmprEnd[1], 1);
						$intervals[$i]['LEN'] = $intLen[1];
						$intervals[$i]['AVVEL'] = round($intAvVel[1]);
						$intervals[$i]['MAXVEL'] = $intMaxVel[1];
						$intervals[$i]['MOVETIME'] = $intMove[1];
						$intervals[$i]['STOPTIME'] = $intMove[1];
						$intervals[$i]['LATB'] = $intLatB[1];
						$intervals[$i]['LONB'] = $intLonB[1];
						$intervals[$i]['LATS'] = $intLatS[1];
						$intervals[$i]['LONS'] = $intLonS[1];
						$intervals[$i]['DT'] = '';
					}
					$i++;
				}
			}
			if (isset($intFBegin[1])){
				$posBegin = strpos($intFBegin[1], ' - ');
				$timeBegin = substr($intFBegin[1], 0, $posBegin);
				$adrBegin = substr($intFBegin[1], $posBegin + 3);
				$adrBegin = preg_replace($patterns, $replacements, $adrBegin);
			}else{
				$timeBegin = '';
				$adrBegin = '';
			}
			if (isset($intFEnd[1])){
				$posEnd = strpos($intFEnd[1], ' - ');
				$timeEnd = substr($intFEnd[1], 0, $posEnd);
				$adrEnd = substr($intFEnd[1], $posEnd + 3);
				$adrEnd = preg_replace($patterns, $replacements, $adrEnd);
			}else{
				$timeEnd = '';
				$adrEnd = '';
			}
			$result = array(
				'ANUM' => $outAnum[1],
				'DTBEG' => $outDtbeg[1],
				'INTERVAL' => $timeBegin . '<br>' . $timeEnd,
				'FBEG' => $adrBegin,
				'FT1' => isset($tmprFBegin[1]) ? round($tmprFBegin[1], 1) : '',
				'FEND' => $adrEnd,
				'FT2' => isset($tmprFEnd[1]) ? round($tmprFEnd[1], 1) : '',
				'FLEN' => isset($intFLen[1]) ? $intFLen[1] : '',
				'FAVVEL' => isset($intFAvVel[1]) ? $intFAvVel[1] : '',
				'FMAXVEL' => isset($intFMaxVel[1]) ? $intFMaxVel[1] : '',
				'FTMOVE' => isset($intFMove[1]) ? $intFMove[1] : '',
				'FTSTOP' => isset($intFStop[1]) ? $intFStop[1] : '',
				'INTERVALS' => $intervals,
			);
		}
		elseif($f_gwx == "repstop"){
			preg_match("/<ANUM>(.+)<\/ANUM>/", $out, $outAnum);
			preg_match("/<DTBEG>(.+)<\/DTBEG>/sUi", $out, $outDtbeg);
			preg_match_all("/<ROW>(.+)<\/ROW>/sUi", $out, $outStops);
			$stops = array();
			if (count($outStops[1]) > 0) {
				$i = 0;
				foreach ($outStops[1] as $key => $val){
					preg_match("/<DT>(.+)<\/DT>/sUi", $val, $stopDt);
					preg_match("/<BEG>(.+)<\/BEG>/sUi", $val, $stopBegin);
					preg_match("/<END>(.+)<\/END>/sUi", $val, $stopEnd);
					preg_match("/<DUR>(.+)<\/DUR>/sUi", $val, $stopDur);
					preg_match("/<AVVEL>(.+)<\/AVVEL>/sUi", $val, $stopAvVel);
					preg_match("/<LEN>(.+)<\/LEN>/sUi", $val, $stopLen);
					preg_match("/<ADDR>(.+)<\/ADDR>/sUi", $val, $stopAddr);
					if ($stopDt[1] == 'Движение' || $stopDt[1] == 'Стоянка'){
						$stops[$i]['DT'] = $stopDt[1];
						$stops[$i]['BEG'] = $stopBegin[1];
						$stops[$i]['END'] = $stopEnd[1];
						$stops[$i]['DUR'] = $stopDur[1];
						$stops[$i]['AVVEL'] = $stopDt[1] == 'Движение' ? round($stopAvVel[1]) : '';
						$stops[$i]['LEN'] = $stopDt[1] == 'Движение' ? $stopLen[1] : '';
						$stops[$i]['ADDR'] = $stopDt[1] == 'Стоянка' ? preg_replace($patterns, $replacements, $stopAddr[1]) : '';
					}else{
						$stops[$i]['DT'] = $stopDt[1];
					}
					$i++;
				}
			}
			$result = array(
				'ANUM' => $outAnum[1],
				'DTBEG' => $outDtbeg[1],
				'STOPS' => $stops,
			);
		}
		elseif($f_gwx == "repcmp"){
			preg_match("/<ANUM>(.+)<\/ANUM>/", $out, $outAnum);
			preg_match("/<DTBEG>(.+)<\/DTBEG>/sUi", $out, $outDtbeg);
			preg_match("/<SHIFT>(.+)<\/SHIFT>/sUi", $out, $outShift);
			preg_match_all("/<ROW>(.+)<\/ROW>/sUi", $out, $outStops);
			$stops = array();
			if (count($outStops[1]) > 0) {
				$i = 0;
				foreach ($outStops[1] as $key => $val){
					preg_match("/<NP>(.+)<\/NP>/sUi", $val, $stopNp);
					preg_match("/<NTRIP>(.+)<\/NTRIP>/sUi", $val, $stopNtrip);
					preg_match("/<DTPLAN>(.+)<\/DTPLAN>/sUi", $val, $stopDtPlan);
					preg_match("/<TP>(.+)<\/TP>/sUi", $val, $stopTp);
					preg_match("/<DTFACT>(.+)<\/DTFACT>/sUi", $val, $stopDtFact);
					preg_match("/<TMPR>(.+)<\/TMPR>/sUi", $val, $stopTmpr);
					preg_match("/<ADDR>(.+)<\/ADDR>/sUi", $val, $stopAddr);
					$stops[$i]['NP'] = isset($stopNp[1]) ? $stopNp[1] : '';
					$stops[$i]['NTRIP'] = isset($stopNtrip[1]) ? $stopNtrip[1] : '';
					$stops[$i]['DTPLAN'] = isset($stopDtPlan[1]) ? $stopDtPlan[1] : '';
					$stops[$i]['TP'] = isset($stopTp[1]) ? $stopTp[1] : '';
					$stops[$i]['DTFACT'] = isset($stopDtFact[1]) ? $stopDtFact[1] : '';
					if (isset($stopTmpr[1])){
						$tmpr = explode('...', $stopTmpr[1]);
						$stops[$i]['TMPR'] = round($tmpr[0], 1) . ' - ' . round($tmpr[1], 1);
					}else{
						$stops[$i]['TMPR'] = '';
					}
					$stops[$i]['ADDR'] = isset($stopAddr[1]) ? preg_replace($patterns, $replacements, $stopAddr[1]) : '';
					$i++;
				}
			}
			$result = array(
				'ANUM' => $outAnum[1],
				'DTBEG' => $outDtbeg[1],
				'SHIFT' => isset($outShift[1]) ? $outShift[1] : '',
				'STOPS' => $stops,
			);
		}
		else{
			$out_mnlat = array();
			$out_mnlon = array();
			$out_mxlat = array();
			$out_mxlon = array();
			preg_match("/<MinLat>(.+)<\/MinLat>/", $out, $out_mnlat);
			$trmnlat = $out_mnlat[1];
			preg_match("/<MinLon>(.+)<\/MinLon>/", $out, $out_mnlon);
			$trmnlon = $out_mnlon[1];
			preg_match("/<MaxLat>(.+)<\/MaxLat>/", $out, $out_mxlat);
			$trmxlat = $out_mxlat[1];
			preg_match("/<MaxLon>(.+)<\/MaxLon>/", $out, $out_mxlon);
			$trmxlon = $out_mxlon[1];
			if (($f_gwx == "Address")&&(substr_count($out,'<Address type="array">') > 0)){
				$out_addr = array();
				preg_match_all("/<Address>(.+)<\/Address>/sUi", $out, $out_addr);
				foreach ($out_addr[1] as $k => $v){
					$sarr[] = array('addr'=>$v);
				}
				$result = array('type'=>'success', 'address'=>$sarr);
			}
			else{
				$result = array('type'=>'success', 'minlat'=>$trmnlat, 'minlon'=>$trmnlon, 'maxlat'=>$trmxlat, 'maxlon'=>$trmxlon);
			}
		}		
	}
	else{
		$result = array('type'=>'error', 'res'=>'Ошибка подключения к серверу');
	}
}
else{
	$result = array('type'=>'error', 'res'=>'Ошибка подключения к серверу');
}

//	Упаковываем данные с помощью JSON
print json_encode($result);
?>