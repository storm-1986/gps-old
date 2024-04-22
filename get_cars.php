<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$user_name = $_GET['su_name'];
//$user_name = 'admin';
@$car_id = $_GET['car'];
@$type = $_GET['type'];
$freg = intval($_GET['flagrep']);
//$freg = 0;
@$sch = $_GET['sch'];
@$smes = $_GET['smes'];
@$sgod = $_GET['sgod'];
@$sour = $_GET['sour'];
@$smin = $_GET['smin'];
@$poch = $_GET['poch'];
@$pomes = $_GET['pomes'];
@$pogod = $_GET['pogod'];
@$poour = $_GET['poour'];
@$pomin = $_GET['pomin'];
@$allday = $_GET['allday'];
@$mash = $_GET['fmash'];
@$mvoz = $_GET['fmvoz'];

$day = date("d");
$mes = date("m");
$year = date("Y");

$regions = array();

$seluser = $conn->query("SELECT OWNER, LCTN FROM SP_USER WHERE LOGIN = '".$user_name."'");

while($data_su = $seluser->fetch( PDO::FETCH_ASSOC )){
	$resowner = $data_su["OWNER"];	
	$reslctn = $data_su["LCTN"];
}
// Запрос, в котором нет условия по влядельцу авто (для пользователей савушкина и админов)
if ($resowner == 0){
	$insuser = " ";
}else{
	$insuser = "OWNER = ".$resowner." AND ";
}

$inscond = "LCTN IN (".$reslctn.") AND PF <> 2";

if ($mash == 1 && $mvoz == 0){
	$inscond = "LCTN IN (".$reslctn.") AND PF <> 1 AND PF <> 2";
}elseif ($mash == 0 && $mvoz == 1){
	$inscond = "LCTN IN (".$reslctn.") AND PF <> 0 AND PF <> 2";
}

$nc = $conn->query("SELECT ANUM FROM SP_CARS WHERE ".$insuser.$inscond." ORDER BY ANUM");

$i = 0;
$count = 0;
while($data_nc = $nc->fetch( PDO::FETCH_ASSOC )){
	$resanum = trim($data_nc["ANUM"]);					/*Запросы для онлайн параметров*/
	$query = "SELECT * FROM GPS_LAST WHERE ANUM = '".$resanum."' AND LASTDATE = '".$year."-".$mes."-".$day."'";
	$resultid = $conn->query($query);
	while($data_lr = $resultid->fetch( PDO::FETCH_ASSOC )){
		$lastlat = $data_lr["LASTLAT"];
		$lastlong = $data_lr["LASTLONG"];
		$lastdate = $data_lr["LASTDATE"];
		$lasttime = $data_lr["LASTTIME"];
		$lastspd = $data_lr["LASTSPEED"];
		@$teldev = $data_lr["TELDRV"];
		if ($teldev == '') $teldev = '-';
		$lastsern = $data_lr["SERN"];

		if ($freg == 0){
			$ar_cars[$i]['lat'] = $lastlat;
			$ar_cars[$i]['lon'] = $lastlong;
			$ar_cars[$i]['text'] = "<b>".$resanum."</b><br/>Номер прибора: ".$lastsern."<br/>Дата и время: ".substr($lastdate, 8, 2).".".substr($lastdate, 5, 2).".".substr($lastdate, 0, 4)." ".$lasttime."</br>Скорость: ".$lastspd." км/ч\n";
			$i++;
		}

// Только для онлайн-параметров
		if ($freg == 2){
			$regions[] = array('title'=>$resanum, 'speed'=>$lastspd, 'date'=>$lastdate, 'time'=>$lasttime, 'tel'=>$teldev);
			$count++;
		}
	}

}
if ($freg == 0){
if ($i > 0){
//	print_r($ar_cars);
	$result = array('type'=>'success', 'cars'=>$ar_cars);
}else{
	$result = array('type'=>'error');
}
}
if ($freg == 2){
if ($count > 0){
	$result = array('type'=>'success', 'regions'=>$regions);
}else{
	$result = array('type'=>'error');
}
}

//	Упаковываем данные с помощью JSON
print json_encode($result);
?>