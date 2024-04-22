<?php
session_start();

include_once "options.php";

if (isset($_GET['exit'])){
	session_destroy();
}
?>

<!doctype html>
<html lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="robots" content="noindex, nofollow"/>
<title>GPS control</title>
<link rel="stylesheet" href="css/design.css"/>
<link rel="stylesheet" href="css/bootstrap.min.css" />
<?php

if (!isset($_SESSION['id']) || $_SESSION['id'] !== session_id()){

if (isset ($_POST['aut'])){
	$rem = array('%', '_', '=');
	$user = str_replace($rem,"",$_POST['log']);
	$user = htmlentities($user, ENT_QUOTES);
	$pswd = md5($_POST['pass']);

	if ($user !== '' AND $pswd !== ''){
		$vu = $conn->query("SELECT * FROM SP_USER WHERE LOGIN = '".$user."' AND PASSWORD = '".$pswd."'");
		$count = 0;

		while ($data_vu = $vu->fetch( PDO::FETCH_ASSOC )){
			$count++;
			$kolpos = $data_vu["COUNTER"];
			$utype = $data_vu["ROLE"];
			$uowner = $data_vu["OWNER"];
			$fails = $data_vu["FAIL"];
			$lctn = $data_vu["LCTN"];
			$urole = $data_vu["ROLENAME"];
			$ukod = $data_vu["KOD"];
			$ukod2 = $data_vu["KOD2"];
		}
		if($fails == $attempts){

			$autmess = "Вы превысили количество попыток для регистрации. Ваша учётная запись заблокирована. Обратитесь за помощью к администратору.";

			include 'form.php';
            exit;	
		}
		if($count == 1){	/*	!!!!!!!!!!!!!!!!!!!!!!!!!!!!	УСПЕШНАЯ АВТОРИЗАЦИЯ	!!!!!!!!!!!!!!!!!!!!!!!!!!!!	*/
			$_SESSION['id'] = session_id();
			$_SESSION['username'] = $user;
			$_SESSION['usertype'] = $utype;
			$_SESSION['userowner'] = $uowner;
			$_SESSION['userrole'] = $urole;
			$_SESSION['userkod'] = $ukod;
			$_SESSION['userkod2'] = $ukod2;
			$_SESSION['lctn'] = $lctn;
			
			$kolpos++;
			
			// Обнуление счётчика неудачных попыток

			$upd = $conn->query("UPDATE SP_USER SET COUNTER = ".$kolpos.", FAIL = 0, LAST_DATE = GETDATE() WHERE LOGIN = '".$user."'");

//			$uowner = $resseluser['owner'];
			
			include_once "topmenu.php";
			include "omaps.php";
		}
        else{		/*	!!!!!!!!!!!!!!!!!!!!!!!!!!!!	НЕУДАЧНАЯ АВТОРИЗАЦИЯ	!!!!!!!!!!!!!!!!!!!!!!!!!!!!	*/
        $vulog = $conn->query("SELECT * FROM SP_USER WHERE LOGIN = '".$user."'");
		$lcount = 0;
		while($data_vulog = $vulog->fetch( PDO::FETCH_ASSOC )){
			$lcount++;
		}
		if($lcount == 1){		/*	!!!!!!!!!!!!!!!!!!!!!!!!!!!!	ПРОВЕРКА НА СУЩЕСТВОВАНИЕ ЛОГИНА	!!!!!!!!!!!!!!!!!!!!!!!!!!!!	*/
			$kolfail = $data_vulog["FAIL"];
			if ($kolfail < $attempts){
				$kolfail++;
//				$updfail = ads_do($rConn, "UPDATE SP_USER SET FAIL = ".$kolfail." WHERE LOGIN = '".$user."'");
				$updfail = $conn->query("UPDATE SP_USER SET FAIL = ".$kolfail." WHERE LOGIN = '".$user."'");
			}
			if ($kolfail == $attempts){
				$autmess = "Вы превысили количество попыток для регистрации. Ваша учётная запись заблокирована. Обратитесь за помощью к администратору.";
				include 'form.php';
            	exit;
			}
			$leftattempts = $attempts - $kolfail;
			$note = " У Вас осталось ".$leftattempts." попытки.";
		}
		else{
			$note = "";
		}

			$autmess = "Неверно указан логин или пароль".$note;
			include 'form.php';
            exit;
        }
	}
	else{
		$autmess = "Введите свои данные";
		include 'form.php';
		exit;
	}
}
else{
	$autmess = "Введите логин и пароль!!!";
	include 'form.php';
	exit;
}
}
else{

include 'topmenu.php';

if (isset($_GET['opis'])) include "opis.php";

if (isset($_GET['ponline']) && (@$_GET['ponline'] == 1) || (@$_GET['ponline'] == 2)) include_once "servis_listauto.php";

if (isset($_GET['ponline']) && $_GET['ponline'] == 3) include_once "monline.php";

if (isset($_GET['admin'])){
	if ($_GET['admin'] == 1){
		include_once "sp_perev.php";
	}
	if ($_GET['admin'] == 2){
		include_once "ausers.php";
	}
	if ($_GET['admin'] == 3){
		include_once "sp_cars.php";
	}
}
if (isset($_GET['osm'])) include_once "omaps.php";

if (isset($_GET['rep'])) include_once "reports.php";

if (isset($_GET['files'])) include_once "files.php";

if (isset($_GET['route'])) include_once "routes.php";

if (isset($_GET['treker'])) include_once "treker.php";

}

?>
</div>
</body>
</html>