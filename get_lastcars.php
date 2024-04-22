<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$car = $_GET['car'];
$user_name = $_GET['su_name'];

$day = date("d");
$mes = date("m");
$year = date("Y");

$query = "SELECT TOP 1 * FROM BD_GBR WHERE ANUM = '".$car."' AND DDATE = '".$year."-".$mes."-".$day."' ORDER BY DTSTAMP DESC";

//echo $query;
$resultid = $conn->query($query);
$i = 0;

while($data_car = $resultid->fetch( PDO::FETCH_ASSOC )){
$i++;
	$lastsern = $data_car["SERN"];
	$lastlat = $data_car["LATT"];
	$lastlon = $data_car["LONGT"];
	$lastdate = $data_car["DDATE"];
	$lasttime = $data_car["TTIME"];
	$lastspd = $data_car["VEL"];
	$lasttmpr = $data_car["TMPR"];
}
if ($i > 0){
	$text = "<b>".$car."</b><br/>Номер прибора: ".$lastsern."<br/>Дата и время: ".substr($lastdate, 8, 2).".".substr($lastdate, 5, 2).".".substr($lastdate, 0, 4)." ".$lasttime."<br/>Скорость: ".$lastspd." км/ч\n";
	$result = array('type'=>'success', 'lat'=>$lastlat, 'lon'=>$lastlon, 'text'=>$text);
}
else{
	$result = array('type'=>'error');
}

//	Упаковываем данные с помощью JSON
print json_encode($result);
?>