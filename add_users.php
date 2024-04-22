<?php
// Добавление пользователя
if (isset($_GET['add']) && isset($_GET['addproc'])){
	$prov = $conn->query("SELECT * FROM SP_USER WHERE LOGIN = '".$_POST['nuser']."'");
	$i = 0;
	while($data_prov = $prov->fetch( PDO::FETCH_ASSOC )){
		$i++;
	}
	if ($i == 0){
		$inslog = $_POST['nuser'];
		$inspass = md5($_POST['npass']);
		$insfio = $_POST['nfio'];
		$insown = $_POST['nowner'];
		$kol_lctn = $_POST['kol_lctn'];
		for ($i = 0; $i <= $kol_lctn; $i++){
			if (isset($_POST['nreg'.$i])){$insreg = $_POST['nreg'.$i].",";}else{$insreg = "";};
			$strlctn .= $insreg;
		}
		$fallctn = substr_replace($strlctn,"",-1);
		$urights = $_POST['nrights'];
		$insus = $conn->query("INSERT INTO SP_USER (LOGIN, PASSWORD, DESCR, ROLE, OWNER, LCTN) VALUES ('$inslog','$inspass','$insfio',$urights,$insown,'$fallctn')");

		//print_r($_POST);
		if ($insus === true) $mess_add_user = "Данные успешно добавлены.";
	}
	else{
		$mess_add_user = "Введённый вами логин уже существует. Повторите ввод.";
	}
?>
		<div class="text-center my-3">
			<div class="h5 mb-4"><?= $mess_add_user; ?></div>
			<input type="button" class="btn btn-outline-success" value="К вводу новых данных" onclick="Backtoadd(2)"/>
			<input type="button" class="btn btn-outline-danger" value="К списку пользователей" onclick="Back(2)"/>
		</div>
<?php
}
else{	// форма для добавления пользователя
?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th scope="col">Логин</th>
				<th scope="col">Пароль</th>
				<th scope="col">ФИО</th>
				<th scope="col">Владелец</th>
				<th scope="col">Регион</th>
				<th scope="col">Права просмотра</th>
			</tr>
		</thead>
		<tr>
			<td>
				<input type="text" name="nuser" class="form-control" maxlength="30" id="nuserid"/>
			</td>
			<td>
				<input type="text" name="npass" class="form-control" id="npassid"/>
			</td>
			<td>
				<input type="text" name="nfio" class="form-control" id="nfioid"/>
			</td>
			<td>
				<select name="nowner" class="form-control">
<?php
				$nown = $conn->query("SELECT * FROM SP_OWNER ORDER BY NAME");
				while($data_nown = $nown->fetch( PDO::FETCH_ASSOC )){
					$owner = $data_nown['OWNER'];
					$oname = $data_nown['NAME'];
					echo"<option value='$owner'>$oname</option>";
				}
?>
				</select>
			</td>
			<td>
<?php
				$nreg = $conn->query("SELECT * FROM SP_LCTN ORDER BY LCTN");
				$i = 0;
				while($data_nreg = $nreg->fetch( PDO::FETCH_ASSOC )){
					$admlctn = $data_nreg['LCTN'];
					$lctnname = iconv("CP1251", "UTF-8", $data_nreg['DSCR']);
					echo"<div class='form-check'><input type='checkbox' class='form-check-input rlabel' name='nreg$i' id='nreg$i' value='$admlctn'"; if ($i == 0) echo" checked"; echo"/><label class='rlabel' for='nreg$i'>$lctnname</label></div>";
					$i++;
				}
?>
				<input type="hidden" name="kol_lctn" value="<?= $i;?>"/>
			</td>
			<td>
				<input type="text" class="form-control" name="nrights" value="0" id="nuserights"/>
			</td>
			</tr>
	</table>
	<div class="form-group text-center mt-2">
		<input type="button" value="Добавить пользователя" class="btn btn-outline-success" onclick="Addproc()"/>
		<input type="button" class="btn btn-outline-danger" value="К списку пользователей" onclick="Back(2)"/>
	</div>
<?php
}
?>