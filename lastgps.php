<?php
include_once "options.php";

$day = date("d");
$mes = date("m");
$year = date("Y");


$onlq = "SELECT * FROM GPS_LAST, GPS_DEV WHERE GPS_LAST.SERN = GPS_DEV.SERN ORDER BY GPS_LAST.SERN";
$resonl = $conn->query($onlq);
?>
<table width="100%" cellpadding="0" cellspacing="0" border="1px">
<tr style="text-align: center; font-weight: bold;"><td>ANUM</td><td>SERN</td><!--<td>STATUS</td>--><td>TOTAL</td><td>ERROR</td><td>LASTDATE</td><td>LASTTIME</td><!--<td>LASTLAT</td><td>LASTLONG</td>--><td>LASTSPEED</td><td>LASTTMPR</td><!--<td>LASTFUEL</td>--><td>TELDEV</td></tr>
<?php
$i = 0;
while($data_res = $resonl->fetch( PDO::FETCH_ASSOC )){
	$i++;
	$ost = $i%2;
	if ($ost == 0){
		$cl = 'style="background: #DCDCDC"';
	}else{
		$cl = '';
	}
	$onlanum = $data_res["ANUM"];
	if ($onlanum == '') $onlanum = "-";
	$onlsern = $data_res["SERN"];
//	$onlstat = $data_res["STATUS"];
	$onltot = $data_res["TOTAL"];
	$onlerr = $data_res["ERR"];
	$onldate = $data_res["LASTDATE"];
	$nicedate = substr($onldate, 6) . "." . substr($onldate, 4, 2) . "." . substr($onldate, 0, 4);
	$onltime = $data_res["LASTTIME"];
/*
	$onllat = $data_res["LASTLAT"];
	$onllong = $data_res["LASTLONG"];
*/
	$onlspd = $data_res["LASTSPEED"];
	$onltmpr = $data_res["LASTTMPR"];
//	$onlfuel = $data_res["LASTFUEL"];
	$onltel = $data_res["TELDEV"];
	if ($onltel == '') $onltel = "-";
	if ($onltmpr < -8 || $onltmpr > 40) $onltmpr = '-';
//	echo "<tr align='center'><td>$onlanum</td><td>$onlsern</td><td>$onlstat</td><td>$onltot</td><td>$onlerr</td><td>$nicedate</td><td>$onltime</td><td>$onllat</td><td>$onllong</td><td>$onlspd</td><td>$onltmpr</td><td>$onlfuel</td><td>$onltel</td></tr>";
	echo "<tr align='center' $cl><td>$onlanum</td><td>$onlsern</td><td>$onltot</td><td>$onlerr</td><td>$nicedate</td><td>$onltime</td><td>$onlspd</td><td>$onltmpr</td><td>$onltel</td></tr>";
}
?>
</table>