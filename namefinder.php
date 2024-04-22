<?

$f_city = $_POST['f_city'];
$f_street = $_POST['f_street'];
$f_house = $_POST['f_house'];
$f_city = str_replace(" ","%20",$f_city);
$f_street = str_replace(" ","%20",$f_street);
/*
$f_city = 'Брест';
$f_street = '';
$f_house = '';
*/

if ($f_city !== '' && $f_street !== '' && $f_house !== ''){
	$url = "http://nominatim.openstreetmap.org/search?q=Беларусь+".$f_city."+улица%20".$f_street."+".$f_house."&format=json";
}elseif ($f_city !== '' && $f_street !== '' && $f_house == ''){
	$url = "http://nominatim.openstreetmap.org/search?q=Беларусь+".$f_city."+улица%20".$f_street."&format=json";
}elseif ($f_city !== '' && $f_street == '' && $f_house == ''){
	$url = "http://nominatim.openstreetmap.org/search?q=Беларусь+".$f_city."&format=json";
}

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
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)");
curl_setopt($ch, CURLOPT_TIMEOUT,        30);
// execute
$text = curl_exec($ch);
if (empty($text)){
	// some kind of an error happened
	die(curl_error($ch));
	curl_close($ch); // close cURL handler
	$result = array('type'=>'error');
}else{
	$info = curl_getinfo($ch);
	curl_close($ch); // close cURL handler
	if (empty($info['http_code'])) {
		die("No HTTP code was returned");
	}else{

//echo $text."<br><br><br><br><br>";

preg_match("/\"lat\":\"(.+)\"/sUi", $text, $out_lat);
preg_match("/\"lon\":\"(.+)\"/sUi", $text, $out_lon);
/*
echo $out_lat[1];
echo $out_lon[1];
*/
if($out_lat[1]&&$out_lon[1]){
	$result = array('type'=>'success', 'lat'=>$out_lat[1], 'lon'=>$out_lon[1], 'answer'=>'');
}else{
	$result = array('type'=>'error');
}
	}
}
print json_encode($result);
?>