<?php
if (isset($_GET['edit']) && isset($_GET['editproc'])){
	$inslog = $_POST['nuser'];
	$inspass = md5($_POST['npass']);
	$insfio = $_POST['nfio'];
	$insown = $_POST['nowner'];
	$kol_lctn = $_POST['kol_lctn'];
	for ($i = 0; $i <= $kol_lctn; $i++){
		if (isset($_POST['nreg'.$i])){
			$insreg = $_POST['nreg'.$i].",";
		}else{
			$insreg = "";
		}
		$strlctn .= $insreg;
	}
	$fallctn = substr_replace($strlctn, "", -1);
	$urights = $_POST['nrights'];
	if (isset($_POST['chpass'])){
		$insedit = $conn->query("UPDATE SP_USER SET LOGIN = '$inslog',PASSWORD = '$inspass',OWNER = $insown,LCTN = '$fallctn',ROLE = $urights,DESCR = '$insfio' WHERE USER_ID = $_POST[uid]");
	}else{
		$insedit = $conn->query("UPDATE SP_USER SET LOGIN = '$inslog',OWNER = $insown,LCTN = '$fallctn',ROLE = $urights,DESCR = '$insfio' WHERE USER_ID = $_POST[uid]");
	}
//print_r($_POST);
?>
<div class="text-center my-3">
	<div class="h5 mb-4">Данные успешно изменены.</div>
	<input type="button" class="btn btn-outline-danger" value="К списку пользователей" onclick="Back(2)"/>
</div>
<?php
}
else{
	if (isset($_POST['Arrus'])){
		$uedit = $_POST["Arrus"];
		$q = "SELECT * FROM SP_USER WHERE";
		foreach($uedit as $k=>$v) $q = $q." (USER_ID = $v) or ";
		$q = substr($q, 0, strlen($q) - 4);
		$r = $conn->query($q);
		while($data_r = $r->fetch( PDO::FETCH_ASSOC )){
			$u_id = $data_r['USER_ID'];
			$u_name = trim($data_r['LOGIN']);
			$u_own = $data_r['OWNER'];
			$user = trim($data_r['DESCR']);
			$u_lctn = $data_r['LCTN'];
			$arr_ulctn = explode(",",$u_lctn);
			$u_role = $data_r['ROLE'];
		}
	?>
		<input type="hidden" name="uid" value="<?= $u_id?>"/>
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
					<input type="text" class="form-control" name="nuser" maxlength="30" id="nuserid" value="<?= $u_name?>"/>
				</td>
				<td>
					<div class="form-check">
						<input type="checkbox" class="form-check-input" value="1" name="chpass" id="chpass" onchange="shpass()"/><label for="chpass">Изменить пароль</label>
					</div>
				<input type="text" class="form-control" name="npass" id="chpassid"/></td>
				<td>
					<input type="text" class="form-control" name="nfio" id="nfioid" value="<?= $user?>"/>
				</td>
				<td>
					<select name="nowner" class="form-control">
	<?php
					$nown = $conn->query("SELECT * FROM SP_OWNER ORDER BY NAME");
					while($data_nown = $nown->fetch( PDO::FETCH_ASSOC )){
						$owner = $data_nown['OWNER'];
						$oname = $data_nown['NAME'];
						echo"<option value='$owner' ";
						if ($u_own == $owner) echo "selected";
						echo">$oname</option>";
					}
	?>
					</select>
				</td>
				<td>
	<?php
					$nreg = $conn->query("SELECT * FROM SP_LCTN ORDER BY LCTN");
					$i = 0;
					while($data_nreg = $nreg->fetch( PDO::FETCH_ASSOC )){
						$admlctn = $data_nreg['LCTN']*1;
						$lctnname = $data_nreg['DSCR'];
						echo"<div class='form-check'><input type='checkbox' name='nreg$i' id='nreg$i' class='form-check-input rlabel' value='$admlctn'"; if (in_array($admlctn,$arr_ulctn)) echo" checked"; echo"/><label class='rlabel' for='nreg$i'>$lctnname</label></div>";
						$i++;
					}

	?>
					<input type="hidden" name="kol_lctn" value="<?= $i;?>"/>
				</td>
				<td><input type="text" class="form-control" name="nrights" id="nuserights" value="<?= $u_role?>"/></td>
			</tr>
		</table>
		<div class="text-center my-3">
			<input type="button" class="btn btn-outline-success" value="Сохранить изменения" onclick="Editproc()"/>
			<input type="button" class="btn btn-outline-danger" value="К списку пользователей" onclick="Back(2)"/>
		</div>
	<?php
	}
	else{
	?>
		<div class="text-center my-3">
			<div class="h5 mb-4">Вы не выбрали ни одной записи.</div>
			<input type="button" class="btn btn-outline-danger" value="К списку пользователей" onclick="Back(2)"/>
		</div>
	<?php
	}
}
?>