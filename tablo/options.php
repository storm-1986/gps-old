<?

// Настройки подключения к SQL server

$serverName = "sqlbd1";

$connectionInfo = array("Database"=>"GPS","ReturnDatesAsStrings"=>true);

$conn = sqlsrv_connect($serverName,$connectionInfo);
if( $conn === false ) die( FormatErrors(sqlsrv_errors()) );


function cod($text){
	$text = iconv("CP1251", "UTF-8", $text);
	return $text; 
}

function FormatErrors( $errors ){
	echo "Error information: <br/>";
	foreach ( $errors as $error )
	{
		echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";
		echo "Code: ".$error['code']."<br/>";
		echo "Message: ".$error['message']."<br/>";
	}
}

// Настройки сокета

$address   = "bpr-serv-prod-2012";
$port      = 2567;
$adsdbpath = "Bpr_serv\\d$\\ADSDB\\GPS\\GPS.add"; // путь к БД ADS
/*
$path      = "c:\\WebServers\\home\\tablo\\www\\"; // путь к папке
$filepath  = "c:/WebServers/home/tablo/www/";
*/
$path      = "c:\\Apache\\sites\\home\\localhost\\www\\tablo\\"; // путь к папке
$filepath  = "c:/Apache/sites/home/localhost/www/tablo/";
?>