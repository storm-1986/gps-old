<?php
// Настройки подключения к SQL server

try {
	$conn = new PDO( "sqlsrv: Server = sqlbd1; Database = GPS; Encrypt = 0; TrustServerCertificate = 1", "laravel", "Lar_2024$");
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
catch( PDOException $e ) {
	die( "Error connecting to SQL Server". $e);
}

// Настройки сокета

$address   = "bpr-serv-prod-2012.bmk.by";
$port      = 2568;
$path      = "c:/Apache/sites/home/localhost/www/"; // путь к папке map
$filepath  = "c:/Apache/sites/home/localhost/www/";
$attempts  = 5;		// Кол-во попыток на ввод пароля

// Настройки трекеров
$tr_list = "8,15,16";	//	Тип трекера из БД (ЧЕРЕЗ ЗАПЯТУЮ БЕЗ ПРОБЕЛОВ)
?>