<?
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";
global $SERVER, $USER, $PASSWD, $DB;

$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ( $rConn == False ){
	$strErrCode = ads_error( );
	$strErrString = ads_errormsg( );
	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\n";
	$result = array('type'=>'error', 'msg'=>'Ошибка связи с сервером');
}
else{
	$route_id = intval($_GET['rid']);
	$o_query = "SELECT * FROM BD_ORDER WHERE ROUTE_ID = ".$route_id." ORDER BY DTPP";
	$res_oquery = ads_do($rConn, $o_query);
	$i = 0;
	while (ads_fetch_row($res_oquery)){
		$i++;
		$r_oid = ads_result($res_oquery, "ORDER_ID");
		$r_post = ads_result($res_oquery, "PP1_ID");
		$r_psklad = ads_result($res_oquery, "PP2_ID");
		$r_zak = ads_result($res_oquery, "PD1_ID");
		$r_zsklad = ads_result($res_oquery, "PD2_ID");
		$r_dt = ads_result($res_oquery, "DTPP");
		$r_kolm = ads_result($res_oquery, "KOLM");
		$r_vb = ads_result($res_oquery, "VB");
		$r_ostat = ads_result($res_oquery, "OSTAT");

		$q_coldoc = "SELECT COUNT (ROUTE_ID) AS \"ord\" FROM BD_DOC WHERE ROUTE_ID = ".$route_id." AND ORDER_ID = ".$r_oid;
		$res_coldoc = ads_do($rConn, $q_coldoc);
		$col_doc = ads_result($res_coldoc,'ord');

		$sp_orders[] = array('oid'=>$r_oid, 'postav'=>$r_post, 'psklad'=>$r_psklad, 'zakaz'=>$r_zak, 'zsklad'=>$r_zsklad, 'dt'=>$r_dt, 'kolm'=>$r_kolm, 'vb'=>$r_vb, 'ostat'=>$r_ostat, 'coldoc'=>$col_doc);
}
	if ($i > 0){
		$result = array('type'=>'success', 'orders'=>$sp_orders);
	}else{

	}
}
print json_encode($result);
?>