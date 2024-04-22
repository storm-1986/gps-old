<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ( $rConn == False ){
	$strErrCode = ads_error( );
	$strErrString = ads_errormsg( );
//	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\n";
	exit;
}

$user_name = $_GET['su_name'];
$car_id = $_GET['car'];
$onldt = $_GET['dt'];

$day = date("d");
$mes = date("m");
$year = date("Y");

$SEL_DB = 'BD_GBR';

$onltr = "SELECT * FROM ".$SEL_DB." WHERE ANUM = '".$car_id."' AND DDATE = '".$year.$mes.$day."' AND TTIME >= '".$onldt."' ORDER BY DTSTAMP";
$restronl = ads_do($rConn, $onltr);

$i = 0;
while (ads_fetch_row($restronl)){
	$trlat = ads_result($restronl, "LATT");
	$trlon = ads_result($restronl, "LONGT");
	$trtime = ads_result($restronl, "TTIME");
	$trspd = ads_result($restronl, "VEL");
	$ar_online[$i]['lat'] = $trlat;
	$ar_online[$i]['lon'] = $trlon;
	$ar_online[$i]['text'] = "<b>".$car_id."</b> онлайн трек<br/>Время: ".$trtime."<br/>Скорость: ".$trspd." км/ч";
	$i++;
}

if ($i >0){
	$result = array('type'=>'success','onl_track'=>$ar_online, 'lat'=>$trlat, 'lon'=>$trlon, 'onltime'=>$trtime, 'spd'=>$trspd);
}else{
	$result = array('type'=>'error');
}
//	Упаковываем данные с помощью JSON
print json_encode($result);
ads_close( $rConn );
?>