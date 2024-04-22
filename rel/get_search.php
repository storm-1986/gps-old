<?
$sity = $_POST['sity'];
//$sity = "Брест";
$street = $_POST['street'];
//$street = "Московская";
$home = $_POST['home'];
//$home = 100;
$tags = $_POST['tags'];
//$tags = "монтана";
$b_minlat = $_POST['minlat'];
//$b_minlat = "52.08604636878003";
$b_minlon = $_POST['minlon'];
//$b_minlon = "23.641033172607422";
$b_maxlat = $_POST['maxlat'];
//$b_maxlat = "52.12289711171449";
$b_maxlon = $_POST['maxlon'];
//$b_maxlon = "23.805828094482422";
$sity = str_replace(" ","%20",$sity);
$street = str_replace(" ","%20",$street);

//$server_url = "http://overpass-api.de/api/interpreter?data=[out:xml];";
$server_url = "http://api.openstreetmap.fr/oapi/interpreter/interpreter?data=";
$server_url2 = "https://nominatim.openstreetmap.org/search?";

if ($tags == ''){
if($sity > '' && $street == '' && $home == ''){
//	$query = "area[name=\"Беларусь\"]->.a;(node(area.a)[\"name:ru\"=\"$sity\"][\"place\"~\"city|town|village|hamlet\"];);out;";
	$query = "country=Беларусь&city=".$sity."&format=json";
	$fl = 1;
}elseif($sity > '' && $street > '' && $home == ''){
//	$query = "area[name=\"$sity\"];(way(area)[\"highway\"~\".*\"][\"name\"~\"$street\",i];>;);out;";
	$query = "country=Беларусь&city=".$sity."&street=".$street."&format=json";
	$fl = 2;
}elseif($sity > '' && $street > '' && $home > ''){
	$query = "area[name=\"$sity\"];(way(area)[~\"addr:street\"~\"$street\",i][\"addr:housenumber\"=\"$home\"];>;);out;";
	$query = "country=Беларусь&city=".$sity."&street=".$home."%20".$street."&format=json";
	$fl = 3;
}
$url = $server_url2.$query;
//echo $url; 
}else{
	$spis = array("дом искусств"=>"arts_centre","пожарный гидрант"=>"fire_hydrant","банкомат"=>"atm","пожарная станция"=>"fire_station","аудитория"=>"auditorium","фонтан"=>"fountain","банк"=>"bank","заправка"=>"fuel","бар"=>"bar","кладбище"=>"grave_yard","скамейка"=>"bench","тренажерный зал"=>"gym","велопарковка"=>"bicycle_parking","холл"=>"hall","прокат велосипедов"=>"bicycle_rental","оздоровительный центр"=>"health_centre","бордель"=>"brothel","больница"=>"hospital","обмен валют"=>"bureau_de_change","отель"=>"hotel","автобусная станция"=>"bus_station","охотничья вышка"=>"hunting_stand","кафе"=>"cafe","мороженное"=>"ice_cream","аренда автомобиля"=>"car_rental","детский сад"=>"kindergarten","прокат автомобиля"=>"car_sharing","библиотека"=>"library","автомойа"=>"car_wash","магазин"=>"market","казино"=>"casino","рыночная площадь"=>"marketplace","кинотеатр"=>"cinema","горная спасательная служба"=>"mountain_rescue","поликлиника"=>"clinic","ночной клуб"=>"nightclub","клуб"=>"club","пансионат"=>"nursery","колледж"=>"college","дом престарелых"=>"nursing_home","общественный центр"=>"community_centre","офис"=>"office","помещение суда"=>"courthouse","парк"=>"park","крематорий"=>"crematorium","стоянка"=>"parking","стоматология"=>"dentist","аптека"=>"pharmacy","врач"=>"doctors","место поклонения"=>"place_of_worship","общежитие"=>"dormitory","полиция"=>"police","питьевая вода"=>"drinking_water","почтовый ящик"=>"post_box","автошкола"=>"driving_school","почтовое отделение"=>"post_office","посольство"=>"embassy","дошкольное учреждение"=>"preschool","телефон экстренных служб"=>"emergency_phone","тюрьма"=>"prison","палатка с едой"=>"fast_food","паб"=>"pub","паромная станция"=>"ferry_terminal","общественное здание"=>"public_building","городской рынок"=>"public_market","приемная"=>"reception_area","место утилизации"=>"recycling","ресторан"=>"restaurant","дом престарелых"=>"retirement_home","сауна"=>"sauna","школа"=>"school","укрытие"=>"shelter","магазин"=>"shop","торговый центр"=>"shopping","сообщество"=>"social_club","студия"=>"studio","супермаркет"=>"supermarket","такси"=>"taxi","телефон"=>"telephone","театр"=>"theatre","туалет"=>"toilets","городская администрация"=>"townhall","университет"=>"university","торговый автомат"=>"vending_machine","ветеринарная клиника"=>"veterinary","усадьба"=>"village_hall","мусорный бак"=>"waste_basket","wi-fi"=>"wifi","молодежный центр"=>"youth_centre","административная граница"=>"administrative"); 
	foreach($spis as $k => $v){
		if ($k == $tags) $fraze = $v;
	}
	if ($fraze == ''){
		$fraze = $tags;
		$query = "[bbox:$b_minlat,$b_minlon,$b_maxlat,$b_maxlon];(node[\"name\"~\"$fraze\",i];area[\"name\"~\"$fraze\",i];way[\"name\"~\"$fraze\",i];rel[\"name\"~\"$fraze\",i];);(._;>;);out;";
	}else{
		$query = "[bbox:$b_minlat,$b_minlon,$b_maxlat,$b_maxlon];(way[amenity=$fraze];node[amenity=$fraze];area[amenity=$fraze];rel[amenity=$fraze];);(._;>;);out;";
	}
	$fl = 4;
	$url = $server_url.$query;
}

//echo $url;
$ch = curl_init(); // create cURL handle (ch)
if (!$ch){
	die("Couldn't initialize a cURL handle");
	$result = array('type'=>'error');
}
// set some cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, 'bmk/serv:serv_user@10.0.0.241:8080');
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)");
curl_setopt($ch, CURLOPT_TIMEOUT,        30);
// execute
$text = curl_exec($ch);
if (empty($text)){
	// some kind of an error happened
	die(curl_error($ch));
	curl_close($ch); // close cURL handler
	$result = array('type'=>'error','msg'=>'Невозможно соединиться с сервером');
}else{
	$info = curl_getinfo($ch);
	curl_close($ch); // close cURL handler
	if (empty($info['http_code'])) {
		die("No HTTP code was returned");
		$result = array('type'=>'error','msg'=>'No HTTP code was returned');	
	}else{
//echo $text;
/*
	if ($fl == 1){			//////////////////////////////////////		ПОИСК ПО НАСЕЛЕННОМУ ПУНКТУ /////////////////////////////////////////
		preg_match_all("/<node(.+)<\/node>/sUi", $text, $ar_cities);
//print_r($ar_cities[1]);
		$i = 0;		
		foreach($ar_cities[1] as $v){
			preg_match ("/lat=\"(.+)\"/sUi", $v, $lat);
			preg_match ("/lon=\"(.+)\"/sUi", $v, $lon);
			preg_match ("/district\"\sv=\"(.+)\"/sUi", $v, $dist);
			preg_match ("/region\"\sv=\"(.+)\"/sUi", $v, $reg);
			if ($dist[1] == '') $dist[1] = "-";
			if ($reg[1] == '') $reg[1] = "-";
			$points[$i]['lat'] = $lat[1];
			$points[$i]['lon'] = $lon[1];
			$points[$i]['dist'] = $dist[1];
			$points[$i]['reg'] = $reg[1];
			if ($i == 0){
				$s_minlat = $lat[1];
				$s_minlon = $lon[1];
				$s_maxlat = $lat[1];
				$s_maxlon = $lon[1];
			}else{
				if ($s_minlat > $lat[1]) $s_minlat = $lat[1];
				if ($s_minlon > $lon[1]) $s_minlon = $lon[1];
				if ($s_maxlat < $lat[1]) $s_maxlat = $lat[1];
				if ($s_maxlon < $lon[1]) $s_maxlon = $lon[1];
			}
			$i++;
		}
		if ($i > 0){
			$result = array('type'=>'success','ar_points'=>$points, 'minlat'=>$s_minlat, 'minlon'=>$s_minlon, 'maxlat'=>$s_maxlat, 'maxlon'=>$s_maxlon, 's_var'=>'1');
		}else{
			$result = array('type'=>'error','msg'=>'Ничего не найдено');	
		}
	}
	if ($fl == 2 || $fl == 3){			//////////////////////////////////////		ПОИСК УЛИЦЫ В ГОРОДЕ     /////////////////////////////////////////
		preg_match_all("/<way\sid=\"\d+\">(.+)<\/way>/sUi", $text, $ar_ways);
//		print_r($ar_ways[1]);
		$i = 0;		
		foreach($ar_ways[1] as $v){
			preg_match_all ("/ref=\"(\d+)\"/sUi", $v, $ar_ids);
//			print_r($ar_ids[1]);
			$i2 = 0;
			foreach($ar_ids[1] as $v2){
				preg_match ("/<node\sid=\"$v2\"\slat=\"(.+)\"/sUi", $text, $lat);
				preg_match ("/<node\sid=\"$v2\"\slat=\".+\"\slon=\"(.+)\"/sUi", $text, $lon);
				$points[$i][$i2]['lat'] = $lat[1];
				$points[$i][$i2]['lon'] = $lon[1];
				if ($i == 0 && $i2 == 0){
					$s_minlat = $lat[1];
					$s_minlon = $lon[1];
					$s_maxlat = $lat[1];
					$s_maxlon = $lon[1];
				}else{
					if ($s_minlat > $lat[1]) $s_minlat = $lat[1];
					if ($s_minlon > $lon[1]) $s_minlon = $lon[1];
					if ($s_maxlat < $lat[1]) $s_maxlat = $lat[1];
					if ($s_maxlon < $lon[1]) $s_maxlon = $lon[1];
				}
				$i2++;
			}
			$i++;
		}
//		print_r($points);
		if ($i > 0){
			$result = array('type'=>'success','ar_points'=>$points, 'minlat'=>$s_minlat, 'minlon'=>$s_minlon, 'maxlat'=>$s_maxlat, 'maxlon'=>$s_maxlon, 's_var'=>'2');
		}else{
			$result = array('type'=>'error','msg'=>'Ничего не найдено');	
		}
	}
*/
	if ($fl !== 4){
preg_match("/\"lat\":\"(.+)\"/sUi", $text, $out_lat);
preg_match("/\"lon\":\"(.+)\"/sUi", $text, $out_lon);
/*
echo $out_lat[1];
echo $out_lon[1];
*/

$points[0]['lat'] = $out_lat[1];
$points[0]['lon'] = $out_lon[1];

if($out_lat[1]&&$out_lon[1]){
	$result = array('type'=>'success','ar_points'=>$points, 's_var'=>'1');
}else{
	$result = array('type'=>'error','msg'=>'Ничего не найдено');
}
	}
	if ($fl == 4){			//////////////////////////////////////		ПОИСК ПО КОНТЕКСТНЫМ СЛОВАМ     /////////////////////////////////////////
		preg_match_all("/<node(.+)<\/node>/sUi", $text, $ar_obj);
		$i = 0;		
		foreach($ar_obj[1] as $v){
			preg_match ("/lat=\"(.+)\"/sUi", $v, $lat);
			preg_match ("/lon=\"(.+)\"/sUi", $v, $lon);
			preg_match ("/name\"\sv=\"(.+)\"/sUi", $v, $name);
			preg_match ("/addr:street\"\sv=\"(.+)\"/sUi", $v, $adr);
			preg_match ("/addr:housenumber\"\sv=\"(.+)\"/sUi", $v, $nhause);
			if ($adr[1] == '') $adr[1] = "-";
			if ($nhause[1] == '') $nhause[1] = "-";
			$points1[$i]['lat'] = $lat[1];
			$points1[$i]['lon'] = $lon[1];
			$points1[$i]['adr'] = $adr[1];
			$points1[$i]['nhs'] = $nhause[1];
			$points1[$i]['name'] = $name[1];
			if ($i == 0){
				$s_minlat1 = $lat[1];
				$s_minlon1 = $lon[1];
				$s_maxlat1 = $lat[1];
				$s_maxlon1 = $lon[1];
			}else{
				if ($s_minlat1 > $lat[1]) $s_minlat1 = $lat[1];
				if ($s_minlon1 > $lon[1]) $s_minlon1 = $lon[1];
				if ($s_maxlat1 < $lat[1]) $s_maxlat1 = $lat[1];
				if ($s_maxlon1 < $lon[1]) $s_maxlon1 = $lon[1];
			}
			$i++;
		}

		preg_match_all("/<way\sid=\"\d+\">(.+)<\/way>/sUi", $text, $ar_ways);
//		print_r($ar_ways[1]);
		$i1 = 0;		
		foreach($ar_ways[1] as $v){
			preg_match_all ("/ref=\"(\d+)\"/sUi", $v, $ar_ids);
//			print_r($ar_ids[1]);
			$i2 = 0;
			foreach($ar_ids[1] as $v2){
				preg_match ("/<node\sid=\"$v2\"\slat=\"(.+)\"/sUi", $text, $lat);
				preg_match ("/<node\sid=\"$v2\"\slat=\".+\"\slon=\"(.+)\"/sUi", $text, $lon);
				$points2[$i1][$i2]['lat'] = $lat[1];
				$points2[$i1][$i2]['lon'] = $lon[1];
				if ($i1 == 0 && $i2 == 0){
					$s_minlat2 = $lat[1];
					$s_minlon2 = $lon[1];
					$s_maxlat2 = $lat[1];
					$s_maxlon2 = $lon[1];
				}else{
					if ($s_minlat2 > $lat[1]) $s_minlat2 = $lat[1];
					if ($s_minlon2 > $lon[1]) $s_minlon2 = $lon[1];
					if ($s_maxlat2 < $lat[1]) $s_maxlat2 = $lat[1];
					if ($s_maxlon2 < $lon[1]) $s_maxlon2 = $lon[1];
				}
				$i2++;
			}
			$i1++;
		}
		if ($s_minlat1 == '' || $s_minlon1 == '' || $s_maxlat1 == '' || $s_maxlon1 == ''){
			$s_minlat = $s_minlat2;
			$s_minlon = $s_minlon2;
			$s_maxlat = $s_maxlat2;
			$s_maxlon = $s_maxlon2;
		}elseif ($s_minlat2 == '' || $s_minlon2 == '' || $s_maxlat2 == '' || $s_maxlon2 == ''){
			$s_minlat = $s_minlat1;
			$s_minlon = $s_minlon1;
			$s_maxlat = $s_maxlat1;
			$s_maxlon = $s_maxlon1;
		}else{
		if ($s_minlat1 <= $s_minlat2){
			$s_minlat = $s_minlat1;
		}else{
			$s_minlat = $s_minlat2;
		}
		if ($s_minlon1 <= $s_minlon2){
			$s_minlon = $s_minlon1; 
		}else{
			$s_minlon = $s_minlon2;
		}
		if ($s_maxlat1 >= $s_maxlat2){
			$s_maxlat = $s_maxlat1; 
		}else{
			$s_maxlat = $s_maxlat2;
		}
		if ($s_maxlon1 >= $s_maxlon2){
			$s_maxlon = $s_maxlon1; 
		}else{
			$s_maxlon = $s_maxlon2;
		}
		}	
//		print_r($points);
		if ($i > 0 || $i1 > 0){
			$result = array('type'=>'success','ar_points1'=>$points1,'ar_points2'=>$points2, 'minlat'=>$s_minlat, 'minlon'=>$s_minlon, 'maxlat'=>$s_maxlat, 'maxlon'=>$s_maxlon, 's_var'=>'3');
		}else{
			$result = array('type'=>'error','msg'=>'Ничего не найдено');	
		}


	}
	}
}
print json_encode($result);
?>