<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "../options.php";

$newqwery = $_POST['newq'];

if (substr($newqwery,0,4) == " AND") $newqwery = substr($newqwery,4);

//$qauto_f = ads_do($rConn, "SELECT * FROM BD_TABLO WHERE ".$newqwery);
$qauto_f = $conn->query("SELECT * FROM BD_TABLO WHERE " . $newqwery);

$ins_tbl = "";
$count = 0;
//while (ads_fetch_row($qauto_f)){
while($data_f = $qauto_f->fetch( PDO::FETCH_ASSOC )){
	$count++;
	if ($count%2 == 0){
		$grtr = '';
	}else{
		$grtr = ' class="grtr"';
	}
	$anum = trim($data_f["ANUM"]);
	$stat = $data_f["STATUS"];
	if ($stat > 100){
		$stat = $data_f["ERROR"];
	}else{
		$stat = 'ОК';
	}
	$skor = intval($data_f["VEL"]);
	$adr = $data_f["ADDR"];
	$adr = iconv("CP1251", "UTF-8", $adr);
	$lctn = $data_f["LCTN"];
//	$qlctn = ads_do($rConn, "SELECT * FROM SP_LCTN WHERE LCTN = $lctn");
	$qlctn = $conn->query("SELECT * FROM SP_LCTN WHERE LCTN = $lctn");
	while($data_lctn = $qlctn->fetch( PDO::FETCH_ASSOC )){
		$nmlctn = $data_lctn["DSCR"];
	}
/*
	$nmlctn = ads_result($qlctn, "DSCR");
	$nmlctn = iconv("CP1251", "UTF-8", $nmlctn);
*/
	$codeown = $data_f["OWNER"];
/*
	$qown = ads_do($rConn, "SELECT * FROM SP_OWNER WHERE OWNER = $codeown");
	$owner = ads_result($qown, "NAME");
	$owner = iconv("CP1251", "UTF-8", $owner);
*/
	$qown = $conn->query("SELECT * FROM SP_OWNER WHERE OWNER = $codeown");

	while($data_own = $qown->fetch( PDO::FETCH_ASSOC )){
		$owner = $data_own["NAME"];
	}
	$ins_tbl .= "<tr".$grtr."><td id=\"$anum\" class=\"atd\" onclick=\"track('$anum')\">$anum</td><td>$stat</td><td>$skor</td><td>$adr</td><td>$nmlctn</td><td>$owner</td></tr>";
}

if ($count > 0){
	$result = array('type'=>'success', 'ins_tbl'=>$ins_tbl);
}else{
	$result = array('type'=>'error', 'msg'=>'Нет автомобилей, подходящих под выбранные фильтры');
}

print json_encode($result);
?>