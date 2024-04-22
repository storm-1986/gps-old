<?php
include_once "options.php";

$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ( $rConn == False ){
	$strErrCode = ads_error( );
	$strErrString = ads_errormsg( );
//	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\n";
	exit;
}

$day = date("d");
$mes = date("m");
$year = date("Y");


$onlq = "SELECT * FROM GPS_LAST, GPS_DEV WHERE GPS_LAST.SERN = GPS_DEV.SERN ORDER BY GPS_LAST.SERN";
$resonl = ads_do($rConn, $onlq);
?>
<table width="100%" cellpadding="0" cellspacing="0" border="1px">
<tr style="text-align: center; font-weight: bold;"><td>ANUM</td><td>SERN</td><!--<td>STATUS</td>--><td>TOTAL</td><td>ERROR</td><td>LASTDATE</td><td>LASTTIME</td><!--<td>LASTLAT</td><td>LASTLONG</td>--><td>LASTSPEED</td><td>LASTTMPR</td><!--<td>LASTFUEL</td>--><td>TELDEV</td></tr>
<?
$i = 0;
while (ads_fetch_row($resonl)){
	$i++;
	$ost = $i%2;
	if ($ost == 0){
		$cl = 'style="background: #DCDCDC"';
	}else{
		$cl = '';
	}
	$onlanum = ads_result($resonl, "ANUM");
	if ($onlanum == '') $onlanum = "-";
	$onlsern = ads_result($resonl, "SERN");
//	$onlstat = ads_result($resonl, "STATUS");
	$onltot = ads_result($resonl, "TOTAL");
	$onlerr = ads_result($resonl, "ERR");
	$onldate = ads_result($resonl, "LASTDATE");
	$nicedate = substr($onldate,6).".".substr($onldate,4,2).".".substr($onldate,0,4);
	$onltime = ads_result($resonl, "LASTTIME");
/*
	$onllat = ads_result($resonl, "LASTLAT");
	$onllong = ads_result($resonl, "LASTLONG");
*/
	$onlspd = ads_result($resonl, "LASTSPEED");
	$onltmpr = ads_result($resonl, "LASTTMPR");
//	$onlfuel = ads_result($resonl, "LASTFUEL");
	$onltel = ads_result($resonl, "TELDEV");
	if ($onltel == '') $onltel = "-";
	if ($onltmpr < -8 || $onltmpr > 40) $onltmpr = '-';
//	echo "<tr align='center'><td>$onlanum</td><td>$onlsern</td><td>$onlstat</td><td>$onltot</td><td>$onlerr</td><td>$nicedate</td><td>$onltime</td><td>$onllat</td><td>$onllong</td><td>$onlspd</td><td>$onltmpr</td><td>$onlfuel</td><td>$onltel</td></tr>";
	echo "<tr align='center' $cl><td>$onlanum</td><td>$onlsern</td><td>$onltot</td><td>$onlerr</td><td>$nicedate</td><td>$onltime</td><td>$onlspd</td><td>$onltmpr</td><td>$onltel</td></tr>";
}
?>
</table>
<?
ads_close( $rConn );
?>