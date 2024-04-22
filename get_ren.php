<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

$su_name = $_GET['su_name'];
$car_id = $_GET['car'];

/*
$su_name = 'admin';
$car_id = '7328';
*/
$of = fopen($filepath."tracks/".$su_name.$car_id.".gpx", "r");
$line = '';
while (!feof($of)){
	$line .= fgets($of);
//	
}
fclose($of);
preg_match_all('/lat="(.+)"/sUi', $line, $out_lat);
preg_match_all('/lon="(.+)"/sUi', $line, $out_lon);
//print_r($out_lon[1]);


if ($of){
	$result = array('type'=>'success','arlat'=>$out_lat[1],'arlon'=>$out_lon[1]);
}else{
	$result = array('type'=>'error');
};

print json_encode($result);
?>