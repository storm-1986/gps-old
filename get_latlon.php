<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Expires: " . date("r"));

include_once "options.php";

if(isset($_POST['su_name'])) $user_name = $_POST['su_name'];
if(isset($_POST['f_gwx'])) $f_gwx = $_POST['f_gwx'];
if(isset($_POST['addr'])) $addr = $_POST['addr'];

$msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<body login=\"".$user_name."\">\n<GWNAV>\n	<SearchLctn>\n		<Flag type=\"string\">".$f_gwx."</Flag>\n		<Address type=\"string\">".$addr."</Address>\n	</SearchLctn>\n</GWNAV>\n</body>\r\n.\r\n";

include "sock.php";

if (isset ($out)){
	$find1 = "<Error>";
	$find2 = "</Error>";
	$pos1 = stripos($out, $find1);
	$pos2 = stripos($out, $find2);
	$resout = substr($out, $pos1+7, $pos2-$pos1-7);
	if ($resout == ''){
		$out_row = array();
		$out_addr = array();
		$out_lat = array();
		$out_lon = array();
		preg_match_all("/<Row>(.+)<\/Row>/sUi", $out, $out_row);
		$i = 0;
		foreach ($out_row[1] as $k => $v){
			$i++;
			preg_match("/<Address>(.+)<\/Address>/sUi", $v, $out_addr);
			preg_match("/<Lat>(.+)<\/Lat>/sUi", $v, $out_lat);
			preg_match("/<Lon>(.+)<\/Lon>/sUi", $v, $out_lon);
			$sarr[] = array('addr'=>$out_addr[1], 'lat'=>$out_lat[1], 'lon'=>$out_lon[1]);
		}
		$result = array('type'=>'success', 'address'=>$sarr, 'count'=>$i);
	}
	else{
		$result = array('type'=>'error', 'res'=>$resout);
	}
}
else{
	$result = array('type'=>'error', 'res'=>'Ошибка подключения к серверу');
}

//	Упаковываем данные с помощью JSON
print json_encode($result);
?>