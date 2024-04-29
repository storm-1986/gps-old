<?php
set_time_limit(0);
ob_implicit_flush();

try{
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket < 0){
		throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
	}
	$result = socket_connect($socket, $address, $port);
	if ($result === false){
		throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
	}else{
		socket_write($socket, $msg, strlen($msg));
		$get = '';
		$out = '';
		while ($get = socket_read($socket, 1024)) {
        	$out .= $get;
        	if (strpos($out, "\r\n.\r\n") !== false) break;
     	}
	}
}
catch (Exception $e){
	echo "Error: ".$e->getMessage();
}
	
if (isset($socket)){
	socket_close($socket);
}
?>