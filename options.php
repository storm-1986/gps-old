<?php
// Настройки подключения к SQL server

try {  
	$conn = new PDO( "sqlsrv: Server = sqlbd1; Database = GPS", NULL, NULL);   
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
}  
catch( PDOException $e ) {  
	die( "Error connecting to SQL Server" );   
}
   
function cod($text){
	$text = iconv("CP1251", "UTF-8", $text);
	return $text; 
}

// Настройки сокета

$address   = "bpr-serv-prod-2012.bmk.by";
$port      = 2567;
$path      = "c:/Apache/sites/home/localhost/www/"; // путь к папке map
$filepath  = "c:/Apache/sites/home/localhost/www/";
$attempts  = 5;		// Кол-во попыток на ввод пароля

// Настройки трекеров
$tr_list = "8,15,16";	//	Тип трекера из БД (ЧЕРЕЗ ЗАПЯТУЮ БЕЗ ПРОБЕЛОВ)
?>