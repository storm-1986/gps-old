<?php
session_start();

include_once "options.php";

if (isset($_GET['exit'])) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: /index.php');
    exit;
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
	if (isset($_POST['aut'])) {

		$user = trim($_POST['log'] ?? '');
		$pass = $_POST['pass'] ?? '';
		$pswd = md5($pass);

		if ($user !== '' && $pswd !== '') {

			// Ищем пользователя по логину+паролю (prepared statement)
			$stmt = $conn->prepare("SELECT * FROM SP_USER WHERE LOGIN = ? AND PASSWORD = ?");
			$stmt->execute([$user, $pswd]);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$count = count($rows);

			if ($count === 1) {

				$data_vu = $rows[0];
				$fails = $data_vu["FAIL"];

				// Проверка блокировки
				if ($fails == $attempts) {
					$autmess = "Вы превысили количество попыток для регистрации. Ваша учётная запись заблокирована. Обратитесь за помощью к администратору.";
					include 'form.php';
					exit;
				}

				// Успешная авторизация — защита от session fixation
				session_regenerate_id(true);

				$_SESSION['id']        = session_id();
				$_SESSION['username']  = trim($data_vu["LOGIN"]);
				$_SESSION['usertype']  = trim($data_vu["ROLE"]);
				$_SESSION['userowner'] = $data_vu["OWNER"];
				$_SESSION['userrole']  = $data_vu["ROLENAME"];
				$_SESSION['userkod']   = $data_vu["KOD"];
				$_SESSION['userkod2']  = $data_vu["KOD2"];
				$_SESSION['lctn']      = $data_vu["LCTN"];

				// Обновляем счётчик посещений и сбрасываем неудачные попытки
				$upd = $conn->prepare(
					"UPDATE SP_USER SET COUNTER = COUNTER + 1, FAIL = 0, LAST_DATE = GETDATE() WHERE LOGIN = ?"
				);
				$upd->execute([$user]);

				include_once "topmenu.php";
				include "omaps.php";
				exit;
			}

			// Неудачная авторизация — считаем попытки только если логин существует
			$stmt = $conn->prepare("SELECT * FROM SP_USER WHERE LOGIN = ?");
			$stmt->execute([$user]);
			$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($userRow !== false) {
				$kolfail = (int)$userRow["FAIL"];

				if ($kolfail < $attempts) {
					$kolfail++;
					$updfail = $conn->prepare("UPDATE SP_USER SET FAIL = ? WHERE LOGIN = ?");
					$updfail->execute([$kolfail, $user]);
				}

				if ($kolfail == $attempts) {
					$autmess = "Вы превысили количество попыток для регистрации. Ваша учётная запись заблокирована. Обратитесь за помощью к администратору.";
					include 'form.php';
					exit;
				}

				$leftattempts = $attempts - $kolfail;
				$note = " У Вас осталось " . $leftattempts . " попытки.";
			} else {
				$note = "";
			}

			$autmess = "Неверно указан логин или пароль" . $note;
			include 'form.php';
			exit;
		}

		$autmess = "Введите свои данные";
		include 'form.php';
		exit;
	}

	$autmess = "Введите логин и пароль!!!";
	include 'form.php';
	exit;
}
else{

include 'topmenu.php';

if (isset($_GET['opis'])) include "opis.php";

if (isset($_GET['ponline']) && (@$_GET['ponline'] == 1) || (@$_GET['ponline'] == 2)) include_once "servis_listauto.php";

if (isset($_GET['ponline']) && $_GET['ponline'] == 3) include_once "monline.php";

if (isset($_GET['admin'])){
	if ($_GET['admin'] == 1){
		include_once "sp_perev.php";
	}elseif ($_GET['admin'] == 2){
		include_once "ausers.php";
	}elseif ($_GET['admin'] == 3){
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