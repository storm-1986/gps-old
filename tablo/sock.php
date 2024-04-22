<?
set_time_limit(0);
ob_implicit_flush();

	try{
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket < 0){
			throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
		} else {
//			echo "OK\n";
		}
//		echo 'Connect socket ... ';
		$result = socket_connect($socket, $address, $port);
		if ($result === false){
			throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
		}else{
//			echo "OK\n";

//		echo "Say to server ($msg) ...";
		socket_write($socket, $msg, strlen($msg));
//		echo "OK<br/>";
//		echo 'Server said: ';
		$out = socket_read($socket, 10000000);
//		echo $out."<br>";
		}
	}

	catch (Exception $e){
//		Вывод ошибок соккета
//		echo "\nError: ".$e->getMessage();
	}
	
	if (isset($socket)){
//		echo 'Close socket ... ';
		socket_close($socket);
//		echo "OK\n";
	}
?>