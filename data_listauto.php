<?php

$user_name = $_SESSION['username'];
$lctn = $_SESSION['lctn'];
$uowner = $_SESSION['userowner'];

$day = date("d");
$mes = date("m");
$year = date("Y");

$list_lctn = explode(",", $lctn);
$list_q = "SELECT LCTN, DSCR FROM SP_LCTN";
$res_listq = $conn->query($list_q);
$i = 0;
$ins_lctn = "";
$ch = "";
while($data_listq = $res_listq->fetch( PDO::FETCH_ASSOC )){
	$reslctn = $data_listq["LCTN"];
	$resname = trim($data_listq["DSCR"]);
	if (in_array($reslctn, $list_lctn)){
		if ((!isset($_POST['filter']) && $i == 0) || isset($_POST['lctn'.$reslctn])){
			$ch = 'checked="checked"';
		}else{
			$ch = "";
		}
		$ins_lctn .= "<div class=\"form-check form-check-inline\"><input type=\"checkbox\" name=\"lctn$reslctn\" id=\"lctn$reslctn\" value=\"$reslctn\" class=\"form-check-input rlabel\" $ch/><label for=\"lctn$reslctn\" class=\"form-check-label rlabel\">$resname</label></div>";
		$i++;
	}
}

$cond_lctn = "";
$i = 0;
foreach ($list_lctn as $key => $value) {
	if (!isset($_POST['filter']) && $i == 0){
		$cond_lctn = "SP_CARS.LCTN = $value OR ";	
	}else{
		if (isset($_POST['lctn'.$value])) $cond_lctn .= "SP_CARS.LCTN = $value OR ";
	}
	$i++;
}

if ($cond_lctn > ""){
	$cond_lctn = "(".substr($cond_lctn, 0,-4).")";
}

// Запрос, в котором нет условия по влядельцу авто (для пользователей савушкина и админов)
if ($uowner == 0 && !isset($_POST['sav'])){
	$insuser = "";
}else{
	$insuser = "AND SP_CARS.OWNER = $uowner";
}

if (isset($_POST['mash']) && isset($_POST['mvoz'])){
	$inscond = "AND (SP_CARS.PF = 0 OR SP_CARS.PF = 1)";
}elseif (isset($_POST['mash']) && !isset($_POST['mvoz'])){
	$inscond = "AND SP_CARS.PF = 0";
}elseif (!isset($_POST['mash']) && isset($_POST['mvoz'])){
	$inscond = "AND SP_CARS.PF = 1";
}elseif (!isset($_POST['mash']) && !isset($_POST['mvoz'])){
	$inscond = "";
}

if ($_GET['ponline'] == 1){
	$znak = '=';
	$order = "NAME, ANUM";
}elseif ($_GET['ponline'] == 2){
	$znak = '<';
	$order = "LASTDATE DESC, LASTTIME DESC";
}

$data_q = "SELECT GPS_LAST.ANUM, SP_OWNER.NAME, GPS_LAST.LASTDATE, GPS_LAST.LASTTIME, GPS_LAST.LASTLAT, GPS_LAST.LASTLONG, GPS_LAST.LASTSPEED FROM SP_CARS, GPS_LAST, SP_OWNER WHERE SP_CARS.OWNER = SP_OWNER.OWNER AND (SP_CARS.ANUM = GPS_LAST.ANUM AND $cond_lctn $insuser $inscond) AND SERN = (SELECT TOP 1 t2.SERN FROM GPS_LAST t2 WHERE t2.ANUM = SP_CARS.ANUM and GPS_LAST.LASTDATE $znak '$year-$mes-$day' ORDER BY t2.LASTDATE DESC) ORDER BY $order";

$nc = $conn->query($data_q);

$i = 0;
$ins = "";

while($data_nc = $nc->fetch( PDO::FETCH_ASSOC )){
	$resanum = trim($data_nc["ANUM"]);
	$resowner = trim($data_nc["NAME"]);
	$reslat = $data_nc["LASTLAT"];
	$reslong = $data_nc["LASTLONG"];
	$resdate = date("d.m.Y", strtotime($data_nc["LASTDATE"]));
	$restime = $data_nc["LASTTIME"];
	$respd = round($data_nc["LASTSPEED"]);
	$i++;
	$ins .= "<tr><th scope='row'>$i</th><td>$resanum</td><td>$resowner</td><td>$resdate $restime</td><td><a href = 'http://pda.brestmilk.by/rel/map.php?cmd=marker(mlat;mlon;color),($reslat;$reslong;red)' target='_blank'>$reslat, $reslong</a></td><td>$respd</td></tr>";
}
?>