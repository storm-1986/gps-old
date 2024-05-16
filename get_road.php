<?php
include_once "options.php";
/*
$address   = "bpr_serv";
$port      = 2568;
*/
$su_name = $_GET['su_name'];
$fromlat = $_GET['fromlat'];
$fromlon = $_GET['fromlon'];
$tolat = $_GET['tolat'];
$tolon = $_GET['tolon'];
$trpoints = $_GET['trpoints'];

if ($trpoints == '0'){
	$msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<body login=\"".$su_name."\">\r\n<SetService>\r\n	<GetGWXRoute>\r\n		<points>".$fromlat.",".$fromlon.",".$tolat.",".$tolon."</points>\r\n	</GetGWXRoute>\r\n</SetService>\r\n</body>\r\n.\r\n";
}else{
	$msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<body login=\"".$su_name."\">\r\n<SetService>\r\n	<GetGWXRoute>\r\n		<points>".$fromlat.",".$fromlon.",".$trpoints.",".$tolat.",".$tolon."</points>\r\n	</GetGWXRoute>\r\n</SetService>\r\n</body>\r\n.\r\n";
}
include "sock.php";

if (isset ($out)){
	$r_koord = array();
	preg_match("/<Error>(.+)<\/Error>/sUi", $out, $out_err);
	preg_match("/<RouteLength>(.+)<\/RouteLength>/sUi", $out, $out_dist);
	if (!isset($out_err[1])){
		preg_match_all("/<Row>(.+)<\/Row>/sUi", $out, $out_row);
		$count = 0;
		foreach ($out_row[1] as $k => $v){
			preg_match("/<Lat>(.+)<\/Lat>/sUi", $v, $out_lat);
			preg_match("/<Lon>(.+)<\/Lon>/sUi", $v, $out_lon);
			$ar_road[$count]['lat'] = $out_lat[1];
			$ar_road[$count]['lon'] = $out_lon[1];
			$count++;
		}
	$result = array('type'=>'success', 't_road'=>$ar_road, 'dist'=>$out_dist[1]);
	}
	else{
		$result = array('type'=>'error', 'res'=>$out_err[1]);
	}
}
else{
	$result = array('type'=>'error', 'res'=>'Ошибка подключения к серверу');
}
print json_encode($result);
?>