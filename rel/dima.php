<?
include_once "options.php";
if ($_GET){
	$msg = implode("---", $_GET);
	$flog = fopen($path."dima.txt", "a+b");
	ftruncate ($flog, 0);
	fwrite($flog, $msg);
	fclose($flog);
}
if ($_POST){
	$msg = implode("---", $_POST);
	$flog = fopen($path."dima.txt", "a+b");
	ftruncate ($flog, 0);
	fwrite($flog, $msg);
	fclose($flog);
}substr
?>