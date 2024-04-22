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

$ar_ferms = array();
$count = 0;

$query = "SELECT * FROM BD_POI WHERE PGrpName = 'Ferm'";
$count = 0;
$resultid = ads_do($rConn, $query);
while (ads_fetch_row($resultid)){
	$fermlat = ads_result($resultid, "Lat");
	$fermlon = ads_result($resultid, "Lon");
	$fermname = ads_result($resultid, "Caption");
    $fermname = iconv("CP1251", "UTF-8", $fermname);
	$fermadr = ads_result($resultid, "Addr");
    $fermadr = iconv("CP1251", "UTF-8", $fermadr);
	$ar_ferms[$count]['lat'] = $fermlat;
	$ar_ferms[$count]['lon'] = $fermlon;
	$ar_ferms[$count]['text'] = "<b>".$fermname."</b><br/>".$fermadr;
    $count++;
}
if ($count > 0){
	$result = array('type'=>'success','ferms'=>$ar_ferms);
}
else{
	$result = array('type'=>'error');
}

//	Упаковываем данные с помощью JSON
print json_encode($result);
ads_close( $rConn );
?>