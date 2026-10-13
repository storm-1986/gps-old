<?php
require_once __DIR__ . '/guard.php';

$day = date("d");
$mes = date("m");
$year = date("Y");


$onlq = "SELECT * FROM GPS_LAST, GPS_DEV WHERE GPS_LAST.SERN = GPS_DEV.SERN ORDER BY GPS_LAST.SERN";
$resonl = $conn->query($onlq);
?>
<table width="100%" cellpadding="2px" cellspacing="0" border="1px">
<tr><th>ANUM</th><th>SERN</th><!--<td>STATUS</td>--><th>TOTAL</th><th>ERROR</th><th>LASTDATE</th><th>LASTTIME</th><!--<td>LASTLAT</td><td>LASTLONG</td>--><th>LASTSPEED</th><th>LASTTMPR</th><!--<td>LASTFUEL</td>--><th>TELDEV</th></tr>
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
	$nicedate = substr($onldate, 8, 2) . "." . substr($onldate, 5, 2) . "." . substr($onldate, 0, 4);
	$onltime = $data_res["LASTTIME"];
/*
	$onllat = $data_res["LASTLAT"];
	$onllong = $data_res["LASTLONG"];
*/
	$onlspd = round($data_res["LASTSPEED"], 1);
	$onltmpr = round($data_res["LASTTMPR"], 1);
//	$onlfuel = $data_res["LASTFUEL"];
	$onltel = $data_res["TELDEV"];
	if ($onltel == '') $onltel = "-";
	if ($onltmpr < -8 || $onltmpr > 40) $onltmpr = '-';
	echo "<tr align='center' $cl><td>$onlanum</td><td>$onlsern</td><td>$onltot</td><td>$onlerr</td><td>$nicedate</td><td>$onltime</td><td>$onlspd</td><td>$onltmpr</td><td>$onltel</td></tr>";
}
?>
</table>