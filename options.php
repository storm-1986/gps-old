<?php

function getEnvValue($key, $default = null) {
    static $env = null;
    
    if ($env === null) {
        $env = [];
        if (file_exists(__DIR__ . '/.env')) {
            $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
                    list($name, $value) = explode('=', $line, 2);
                    $env[trim($name)] = trim($value);
                }
            }
        }
    }
    
    return isset($env[$key]) ? $env[$key] : $default;
}

// Настройки подключения к SQL server

try {
    $conn = new PDO(
        "sqlsrv:Server=" . getEnvValue('DB_HOST') . ";Database=" . getEnvValue('DB_NAME') . ";Encrypt=0;TrustServerCertificate=1", getEnvValue('DB_USER'), getEnvValue('DB_PASS')
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $e ) {
	die( "Error connecting to SQL Server". $e);
}

// Настройки сокета

$address   = getEnvValue('SOCKET_ADDRESS');
$port      = getEnvValue('SOCKET_PORT');

$attempts  = 5;		// Кол-во попыток на ввод пароля
// Настройки трекеров
$tr_list = "8,15,16";	//	Тип трекера из БД (ЧЕРЕЗ ЗАПЯТУЮ БЕЗ ПРОБЕЛОВ)