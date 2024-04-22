<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ($rConn == False){
	$strErrCode = ads_error();
	$strErrString = ads_errormsg();
//	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\r\n";
	exit;
}
$newqwery = $_POST['newq'];

if (substr($newqwery,0,4) == " AND") $newqwery = substr($newqwery,4);

$f_query = "SELECT * FROM BD_TABLO WHERE ".$newqwery;

$qauto_f = ads_do($rConn, $f_query);

$ins_tbl = "";
$count = 0;
while (ads_fetch_row($qauto_f)){
	$count++;
	if ($count%2 == 0){
		$grtr = '';
	}else{
		$grtr = ' class="grtr"';
	}
	$anum = trim(ads_result($qauto_f, "ANUM"));
	$stat = ads_result($qauto_f, "STATUS");
	if ($stat > 100){
		$stat = ads_result($qauto_f, "ERROR");
		$stat = iconv("CP1251", "UTF-8", $stat);
	}else{
		$stat = 'ОК';
	}
	$skor = intval(ads_result($qauto_f, "VEL"));
	$adr = ads_result($qauto_f, "ADDR");
	$adr = iconv("CP1251", "UTF-8", $adr);
	$lctn = ads_result($qauto_f, "LCTN");
	$qlctn = ads_do($rConn, "SELECT * FROM SP_LCTN WHERE LCTN = $lctn");
	$nmlctn = ads_result($qlctn, "DSCR");
	$nmlctn = iconv("CP1251", "UTF-8", $nmlctn);
	$codeown = ads_result($qauto_f, "OWNER");
	$qown = ads_do($rConn, "SELECT * FROM SP_OWNER WHERE OWNER = $codeown");
	$owner = ads_result($qown, "NAME");
	$owner = iconv("CP1251", "UTF-8", $owner);
	$ins_tbl .= "<tr".$grtr."><td id=\"$anum\" class=\"atd\" onclick=\"track('$anum')\">$anum</td><td>$stat</td><td>$skor</td><td>$adr</td><td>$nmlctn</td><td>$owner</td></tr>";
}

if ($count > 0){
	$result = array('type'=>'success', 'ins_tbl'=>$ins_tbl);
}else{
	$result = array('type'=>'error', 'msg'=>'Нет автомобилей, подходящих под выбранные фильтры');
}

print json_encode($result);
ads_close($rConn);
?>