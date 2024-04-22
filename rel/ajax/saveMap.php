<?php

include_once "../options.php";
$port = 1568;

//	define('UPLOAD_DIR', '../savedMaps/');

$savedMap = $_POST['savedMap'];
$p_qid =  $_POST['p_qid'];
$savedMap = str_replace('data:image/png;base64,', '', $savedMap);
$savedMap = str_replace(' ', '+', $savedMap);

$msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<body>\n<SetService>\n	<PutOSMImage>\n		<QID>".$p_qid."</QID>\n		<IMG type=\"string\">".$savedMap."</IMG>\n	</PutOSMImage>\n</SetService>\n</body>\r\n.\r\n";
echo $msg;
include "../sock.php";
if (isset ($out)){
	$find1 = "<Error>";
	$find2 = "</Error>";
	$pos1 = stripos($out, $find1);
	$pos2 = stripos($out, $find2);
	$resout = substr($out, $pos1+7, $pos2-$pos1-7);
	if ($resout !== '') echo "<script type=\"text/javascript\">alert('$resout');</script>";
}
/*
	$data = base64_decode($savedMap);
	$file = UPLOAD_DIR . uniqid() . '.png';
	$success = file_put_contents($file, $data);
	print $success ? $file : 'Unable to save the file.';
*/	
?>