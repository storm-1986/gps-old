<div class="container-fluid">
	<div class="h4 my-3">Работа с трекерами</div>

<form id="f1" name="mform" method="post">
<?
if (isset($_GET['del'])){							/*УДАЛЕНИЕ ТРЕКЕРА*/
	if (isset($_POST['Arrtr'])){
		$trdel = $_POST["Arrtr"];		
		foreach($trdel as $k=>$v){
			$qrtr = $conn->query("DELETE FROM BD_TREKLOG WHERE SERN = $v");
			$qdeltrk = $conn->query("UPDATE GPS_DEV SET ANUM='', DEVDT = GETDATE() WHERE SERN = $v");
			$qlogdeltrk = $conn->query("INSERT INTO BD_LOG (TYPE, MSG) VALUES (40, 'Delete trecker ".$v.", user: ".$_SESSION['username']."')");
			$delcmd = $conn->query("INSERT INTO GPS_CMD (SERN, CMDSTS, CMD) VALUES ($v,0,'ANUM=')");
		}
		$mess_del_tr = "Трекер успешно снят";
	}
	else{
		$mess_del_tr = "Вы не выбрали ни одного трекера";
	}
?>
  		<div class="text-center my-3">
			<div class="h5 mb-4"><?= $mess_del_tr; ?></div>
			<input type="button" class="btn btn-outline-danger" value="К списку трекеров" id="back" onclick="Backtr()"/>
		</div>
<?
}
else{
if (isset($_GET['add'])){							/*ДОБАВЛЕНИЕ ТРЕКЕРА*/
	if (isset($_GET['addtrproc'])){
		$newanum = $_POST['car_id'];
		$newsern = $_POST['treker_id'];
		$newnoteads = $_POST['note'];
		$q_perev = "SELECT OWNER FROM SP_CARS WHERE ANUM = '".$newanum."'";
		$resq_perev = $conn->query($q_perev);

		while($data_resqp = $resq_perev->fetch( PDO::FETCH_ASSOC )){
			$newperev = $data_resqp["OWNER"];
		}
		$newnote = $_POST['note'];
		$qregtrk = "UPDATE GPS_DEV SET ANUM = '".$newanum."', DEVDT = GETDATE() WHERE SERN = ".$newsern;
		$resqregtrk = $conn->query($qregtrk);
		if($resq_perev === false){
			$mess_add_tr = "При регистрации трекера произошла ошибка. Обратитесь к администратору.";
		}
		else{
			$qlogregtrk = $conn->query("INSERT INTO BD_LOG (TYPE, MSG) VALUES (30, 'Register trecker: ".$newsern.", anum: ".$newanum.", perev: ".$newperev.", note: ".$newnoteads.", user: ".$_SESSION['username']."')");
			$qnote = $conn->query("INSERT INTO BD_TREKLOG (ANUM, SERN, OWNER, NOTES, LOGIN) VALUES ('$newanum',$newsern,$newperev,'$newnote','$_SESSION[username]')");
			$inscmd = $conn->query("INSERT INTO GPS_CMD (SERN, CMDSTS, CMD) VALUES ($newsern,0,'ANUM=$newanum')");
			$mess_add_tr = "Трекер успешно зарегистрирован.";
		}
?>
  		<div class="text-center my-3">
			<div class="h5 mb-4"><?= $mess_add_tr; ?></div>
			<input type="button" class="btn btn-outline-danger" value="К списку трекеров" id="back" onclick="Backtoaddtr()"/>
		</div>
<?

	}
	else{
##													Форма добавления трекера
?>
<table id="tpcars" class="table table-bordered text-center">
	<thead>
		<tr>
			<th width="33%">Машина</th><th>Номер трекера</th><th>Примечание</th>
		</tr>
	</thead>
	<tr>
		<td>
			<div class="form-group">
				<? include 'selcars.php'; ?>
			</div>
			<div class="form-group">
				<input type="button" id="auto_btn_add" class="btn btn-outline-dark" value="Добавить машину в список">
			</div>
		<div id="new_auto" class="hid_add">
		<table>
			<tr>
				<td class="text-right" width="50%">
					Номер машины <span style="color: red;"> (*)</span>
				</td>
				<td>
					<input type="text" id="add_auto" class="form-control" maxlength="8"/>
				</td>
		</tr>
		<tr>
			<td class="text-right">
				Местоположение<span style="color: red;"> (*)</span>
			</td>
			<td>
				<select name="n_carlctn" size="1" id="n_carlctn" class="form-control">
					<option value="">Выбрать</option>
<?
			$qnlctn = "SELECT * FROM SP_LCTN ORDER BY DSCR";
			$resqnlctn = @$conn->query($qnlctn);
			while($data_lctn = @$resqnlctn->fetch( PDO::FETCH_ASSOC )){
				$s_clctn = $data_lctn["LCTN"];
				$s_nlctn = trim($data_lctn["DSCR"]);
				$s_nlctn = iconv("windows-1251","utf-8","$s_nlctn");
				echo"<option value=$s_clctn>$s_nlctn</option>";
			}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="text-right">
				Перевозчик<span style="color: red;"> (*)</span><br/> Введите первые символы названия перевозчика и выберите нужный вариант. Если нужный перевозчик не найден, нажмите кнопку "Добавить перевозчика", добавьте нового и повторите действие, описанное выше.
		</td>
		<td>
<?
			$qper = "SELECT * FROM SP_OWNER ORDER BY NAME";
			$resqper = $conn->query($qper);
			$list_perev = '';
			while($data_qper = @$resqper->fetch( PDO::FETCH_ASSOC )){
				$tr_idper = $data_qper["OWNER"];
				$tr_per = trim($data_qper["NAME"]);
				$list_perev .= "{text: '".$tr_per."', value: '".$tr_idper."'},";
			}
?>
			<script type='text/javascript'>
				$(function() { $('#perev2').immybox({ choices: [<?echo $list_perev;?>]}); });
			</script>
			<div class="form-group">
				<input type="text" id="perev2" class="form-control" />
			</div>
			<div class="form-group">
				<input id="btn_perev_add" type="button" class="btn btn-outline-dark" value="Добавить перевозчика">
			</div>
			<div id="new_perev" class="hid_add">
				<div class="form-group">
					<input type="text" id="add_perev" class="form-control" maxlength="50" onkeydown="imit_click(event)"/>
				</div>
				<div class="form-group">
					<input type="button" class="btn btn-outline-success" value="Добавить перевозчика" onclick="add_newp()"/>
				</div>
			</div>
		</td>
		</tr>
		<tr>
		<td class="text-right">
			Машина описание
		</td>
		<td>
			<input type="text" maxlength="16" id="n_aname" class="form-control" />
		</td>
		</tr>
		<tr>
		<td class="text-right">
			Водитель ФИО
		</td>
		<td>
			<input type="text" maxlength="32" id="n_driver" class="form-control" />
		</td>
		</tr>
		<tr>
		<td class="text-right">
			№ телефона водителя
		</td>
		<td>
			<input type="text" maxlength="13" id="n_tel" class="form-control" />
		</td>
		</tr>
		<tr>
		<td class="text-right">
			Холод. установка
		</td>
		<td>
			<input type="text" maxlength="16" id="n_holod" class="form-control" />
		</td>
		</tr>
		</table>
		<div class="form-group mt-3">
			<input type="button" class="btn btn-outline-success" value="Добавить машину" onclick="add_newa()"/><br/>
		</div>
		</div>
		</td>
		<td>
<?
			$spl_trlist = explode(",", $tr_list); // $tr_list - список в файле options.php 
			foreach ($spl_trlist as $k => $v){
				$res_trlist .= "(ANUM ='' AND DEV = ".$v.") OR ";
			}
			$res_trlist = substr($res_trlist, 0, -4);
			$qtrk = "SELECT SERN FROM GPS_DEV WHERE ".$res_trlist." ORDER BY SERN";

			$resqtrk = $conn->query($qtrk);

			$list_treker = '';
			while($data_trk = $resqtrk->fetch( PDO::FETCH_ASSOC )){
				$sern = trim($data_trk["SERN"]);
				$list_treker .= "{text: '".$sern."', value: '".$sern."'},";
			}
?>
		<script type='text/javascript'>
			$(function() { $('#treker_id').immybox({ choices: [<?echo $list_treker;?>]}); });
		</script>
			<input type="text" name="treker_id" id="treker_id" class="form-control" />
		</td>
		<td>
			<input id="note" name="note" type="text" maxlength="200" class="form-control" />
		</td>
	</tr>
</table>

<div class="row">
	<div class="col-6 text-right">
		<div class="form-group">
			<input type="button" class="btn btn-outline-success" value="Зарегистрировать" onclick="Addtrproc()"/>
		</div>
	</div>
	<div class="col-6">
		<div class="form-group">
			<input type="button" class="btn btn-outline-danger" value="Назад" onclick="Backtr()"/>
		</div>
	</div>
</div>

<?
	}
}
else{																/*СПИСОК ТРЕКЕРОВ*/

if ((@$_GET['sort'] == 'trnum')&&($_GET['trtype'] == 1)){
	$tr_sort = "ANUM";
}
elseif ((@$_GET['sort'] == 'trnum')&&($_GET['trtype'] == 2)){
	$tr_sort = "ANUM DESC";
}
elseif ((@$_GET['sort'] == 'trsern')&&($_GET['trtype'] == 1)){
	$tr_sort = "SERN";
}
elseif ((@$_GET['sort'] == 'trsern')&&($_GET['trtype'] == 2)){
	$tr_sort = "SERN DESC";
}
elseif ((@$_GET['sort'] == 'trperev')&&($_GET['trtype'] == 1)){
	$tr_sort = "OWNER";
}
elseif ((@$_GET['sort'] == 'trperev')&&($_GET['trtype'] == 2)){
	$tr_sort = "OWNER DESC";
}
elseif ((@$_GET['sort'] == 'trlog')&&($_GET['trtype'] == 1)){
	$tr_sort = "LOGIN";
}
elseif ((@$_GET['sort'] == 'trlog')&&($_GET['trtype'] == 2)){
	$tr_sort = "LOGIN DESC";
}
elseif ((@$_GET['sort'] == 'trdate')&&($_GET['trtype'] == 1)){
	$tr_sort = "DT";
}
elseif ((@$_GET['sort'] == 'trdate')&&($_GET['trtype'] == 2)){
	$tr_sort = "DT DESC";
}else{
	$tr_sort = "DT DESC";
}

$journ = $conn->query("SELECT * FROM BD_TREKLOG ORDER BY ".$tr_sort);

$i = 0;
while($data_tr = $journ->fetch( PDO::FETCH_ASSOC )){
	$i++;
	if ($i == 1){
?>
	<div class="row">
		<div class="col-6 text-right">
			<div class="form-group">
				<input type="button" class="btn btn-outline-success" value="Регистрация трекера" onclick="RegTr()"/>
			</div>
		</div>
		<div class="col-6">
			<div class="form-group">
				<input type="button" class="btn btn-outline-danger" value="Снятие трекера" onclick="RemTr()"/>
			</div>
		</div>
	</div>

	<table id="tpcars" class="table table-striped text-center">
	<thead>
	<tr>
		<th scope="col">
			<span id="snum" class="zsort" onclick="trsort('trnum')">Номер машины</span>
			<input type="hidden" name="ntrsort" id="ntrsort" value="<?if ((@$_GET['sort'] == 'trnum')&&($_GET['trtype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'trnum')&&($_GET['trtype'] == 2)){echo"2";}else{echo"0";}?>"/>
		</th>
		<th scope="col">
			<span id="ssern" class="zsort" onclick="trsort('trsern')">Номер трекера</span>
			<input type="hidden" name="ttrsort" id="ttrsort" value="<?if ((@$_GET['sort'] == 'trsern')&&($_GET['trtype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'trsern')&&($_GET['trtype'] == 2)){echo"2";}else{echo"0";}?>"/>
		</th>
		<th scope="col">
			<span id="zperev" class="zsort" onclick="trsort('trperev')">Перевозчик</span>
			<input type="hidden" name="ptrsort" id="ptrsort" value="<?if ((@$_GET['sort'] == 'trperev')&&($_GET['trtype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'trperev')&&($_GET['trtype'] == 2)){echo"2";}else{echo"0";}?>"/>
		</th>
		<th scope="col">Примечание</th>
		<th scope="col">
			<span id="slog" class="zsort" onclick="trsort('trlog')">Логин</span>
			<input type="hidden" name="ltrsort" id="ltrsort" value="<?if ((@$_GET['sort'] == 'trlog')&&($_GET['trtype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'trlog')&&($_GET['trtype'] == 2)){echo"2";}else{echo"0";}?>"/>
		</th>
		<th scope="col">
			<span id="sdt" class="zsort" onclick="trsort('trdate')">Дата</span>
			<input type="hidden" name="dtrsort" id="dtrsort" value="<?if ((@$_GET['sort'] == 'trdate')&&($_GET['trtype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'trdate')&&($_GET['trtype'] == 2)){echo"2";}else{echo"0";}?>"/>
		</th>
		<th scope="col">Отметить</th>
	</tr>
	</thead>
<?
	}
	$tr_anum = $data_tr['ANUM'];
	$tr_sern = $data_tr['SERN'];
	$tr_perev = $data_tr['OWNER'];
	$name_per = $conn->query("SELECT * FROM SP_OWNER WHERE OWNER = ".$tr_perev);

	while($data_nper = $name_per->fetch( PDO::FETCH_ASSOC )){
		$tr_nperev = $data_nper['NAME'];
	}
	$tr_note = $data_tr['NOTES'];
	$tr_login = $data_tr['LOGIN'];
	$tr_dt = substr($data_tr['DT'],8,2).".".substr($data_tr['DT'],5,2).".".substr($data_tr['DT'],0,4)." ".substr($data_tr['DT'],11,5);
	echo"<tr><td>$tr_anum</td><td>$tr_sern</td><td>$tr_nperev</td><td>$tr_note</td><td>$tr_login</td><td>$tr_dt</td><td><input type='checkbox' name='Arrtr[]' value='$tr_sern'></td></tr>";
}

	echo ($i > 0) ?  "</table>" : "<div class='alert alert-danger' role='alert'>Зарегистрированных трекеров не найдено</div>";
?>
	<div class="row">
		<div class="col-6 text-right">
			<div class="form-group">
				<input type="button" class="btn btn-outline-success" value="Регистрация трекера" onclick="RegTr()"/>
			</div>
		</div>
		<div class="col-6">
			<div class="form-group">
				<input type="button" class="btn btn-outline-danger" value="Снятие трекера" onclick="RemTr()"/>
			</div>
		</div>
	</div>
<?
}
}
?>
</form>
</div>