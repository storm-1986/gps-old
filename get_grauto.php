<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$user_name = $_POST['u_name'];
$user_owner = $_POST['u_owner'];
$mash = $_POST['mash'];
$mvoz = $_POST['mvoz'];
$sav = $_POST['sav'];
$lctn = $_POST['lctn'];

$day = date("d");
$mes = date("m");
$year = date("Y");

$list_lctn = explode(",", $lctn);

$cond_lctn = "";
foreach ($list_lctn as $key => $value) {
	$cond_lctn .= "SP_CARS.LCTN = $value OR ";
}

$cond_lctn = "(".substr($cond_lctn, 0,-4).") AND ";
//echo $cond_lctn;

// Запрос, в котором нет условия по влядельцу авто (для пользователей савушкина и админов)
if ($user_owner == 0 && $_POST['sav'] == 0){
	$insuser = "";
}else{
	$insuser = "SP_CARS.OWNER = $user_owner AND ";
}

if ($_POST['mash'] == 1 && $_POST['mvoz'] == 1){
	$inscond = "(SP_CARS.PF = 0 OR SP_CARS.PF = 1)";
}elseif ($_POST['mash'] == 1 && $_POST['mvoz'] == 0){
	$inscond = "SP_CARS.PF = 0";
}elseif ($_POST['mash'] == 0 && $_POST['mvoz'] == 1){
	$inscond = "SP_CARS.PF = 1";
}else{
	$inscond = "";
}

$data_q = "SELECT GPS_LAST.ANUM, SP_OWNER.NAME, GPS_LAST.LASTDATE, GPS_LAST.LASTTIME, GPS_LAST.LASTLAT, GPS_LAST.LASTLONG, GPS_LAST.LASTSPEED, SP_CARS.PF FROM SP_CARS, GPS_LAST, SP_OWNER WHERE SP_CARS.OWNER = SP_OWNER.OWNER AND (SP_CARS.ANUM = GPS_LAST.ANUM AND $cond_lctn $insuser $inscond) AND SERN = (SELECT TOP 1 t2.SERN FROM GPS_LAST t2 WHERE t2.ANUM = SP_CARS.ANUM and GPS_LAST.LASTDATE = '$year-$mes-$day' ORDER BY t2.LASTDATE DESC) ORDER BY ANUM";

//echo $data_q;

$nc = $conn->query($data_q);

$gr_cars = array();
$i = 0;
while($data_nc = $nc->fetch( PDO::FETCH_ASSOC )){
	$resanum = trim($data_nc["ANUM"]);
	$resowner = trim($data_nc["NAME"]);
	$reslat = $data_nc["LASTLAT"];
	$reslong = $data_nc["LASTLONG"];
	$restype = $data_nc["PF"];
	$resdate = date("d.m.Y", strtotime($data_nc["LASTDATE"]));
	$restime = $data_nc["LASTTIME"];
	$respd = round($data_nc["LASTSPEED"]);
	$gr_cars[$i]['lat'] = $reslat;
	$gr_cars[$i]['lon'] = $reslong;
	$gr_cars[$i]['type'] = $restype;
	$gr_cars[$i]['text'] = "<b>".$resanum."</b> (".$resowner.")";
	$i++;
}
if ($i > 0){
	$result = array('type'=>'success','gr_cars'=>$gr_cars);
}
else{
	$result = array('type'=>'error');
}

//	Упаковываем данные с помощью JSON
print json_encode($result);
?>