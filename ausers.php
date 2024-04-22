<div class="container-fluid">
	<div class=" h4 my-3">
		Управление пользователями
	</div>
	<form id="f1" name="mform" method="post">
<?php
if (isset($_GET['del'])){
	include "del_users.php";
}
elseif(isset($_GET['add'])){
	include "add_users.php";
}
elseif (isset($_GET['edit'])){
	include "edit_users.php";
}
else{
?>
	<div class="form-group text-center">
		<input type="button" class="btn btn-outline-success" value="Добавить пользователя" onclick="Addall(2)"/>
		<input type="button" class="btn btn-outline-dark" value="Редактировать пользователя" onclick="Editall(2)"/>
		<input type="button" class="btn btn-outline-danger" value="Удалить отмеченные" onclick="Remall(2)"/>
	</div>
	<table class="table table-hover text-center">
		<thead>
		<tr>
			<th scope="col">
				<span id="un" class="zsort" onclick="sort('num')">№</span>
				<input type="hidden" name="nsort" id="nsort" value="<?php if ((@$_GET['sort'] == 'num')&&($_GET['stype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'num')&&($_GET['stype'] == 2)){echo"2";}else{echo"0";}?>"/>
			</th>
			<th scope="col">Отметить</th>
			<th class="tdol1">
				<span id="ulog" class="zsort" onclick="sort('log')">Логин</span>
				<input type="hidden" name="lsort" id="lsort" value="<?php if ((@$_GET['sort'] == 'log')&&($_GET['stype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'log')&&($_GET['stype'] == 2)){echo"2";}else{echo"0";}?>"/>
			</th>
			<th scope="col">
				<span id="ufio" class="zsort" onclick="sort('fio')">Пользователь</span>
				<input type="hidden" name="fsort" id="fsort" value="<?php if ((@$_GET['sort'] == 'fio')&&($_GET['stype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'fio')&&($_GET['stype'] == 2)){echo"2";}else{echo"0";}?>"/>
			</th>
			<th scope="col">
				<span id="upos" class="zsort" onclick="sort('pos')">Посещения</span>
				<input type="hidden" name="psort" id="psort" value="<?php if ((@$_GET['sort'] == 'pos')&&($_GET['stype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'pos')&&($_GET['stype'] == 2)){echo"2";}else{echo"0";}?>"/>
			</th>
			<th scope="col">
				<span id="ulpos" class="zsort" onclick="sort('plast')">Последнее посещение</span>
				<input type="hidden" name="dsort" id="dsort" value="<?php if ((@$_GET['sort'] == 'plast')&&($_GET['stype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'plast')&&($_GET['stype'] == 2)){echo"2";}else{echo"0";}?>"/>
			</th>
			<th scope="col">
				<span id="ucode" class="zsort" onclick="sort('code')">Код владельца</span>
				<input type="hidden" name="csort" id="csort" value="<?php if ((@$_GET['sort'] == 'code')&&($_GET['stype'] == 1)){echo"1";}elseif ((@$_GET['sort'] == 'code')&&($_GET['stype'] == 2)){echo"2";}else{echo"0";}?>"/>
			</th>
			<th  scope="col">Код региона</th>
			<th  scope="col">Права просмотра</th>
		</tr>
		</thead>
<?php
	if ((@$_GET['sort'] == 'num')&&($_GET['stype'] == 1)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY USER_ID DESC");
	}
	elseif ((@$_GET['sort'] == 'num')&&($_GET['stype'] == 2)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY USER_ID");
	}
	elseif ((@$_GET['sort'] == 'log')&&($_GET['stype'] == 1)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY LOGIN");
	}
	elseif ((@$_GET['sort'] == 'log')&&($_GET['stype'] == 2)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY LOGIN DESC");
	}
	elseif ((@$_GET['sort'] == 'fio')&&($_GET['stype'] == 1)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY DESCR DESC");
	}
	elseif ((@$_GET['sort'] == 'fio')&&($_GET['stype'] == 2)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY DESCR");
	}
	elseif ((@$_GET['sort'] == 'pos')&&($_GET['stype'] == 1)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY COUNTER DESC");
	}
	elseif ((@$_GET['sort'] == 'pos')&&($_GET['stype'] == 2)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY COUNTER");
	}
	elseif ((@$_GET['sort'] == 'plast')&&($_GET['stype'] == 1)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY LAST_DATE DESC");
	}
	elseif ((@$_GET['sort'] == 'plast')&&($_GET['stype'] == 2)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY LAST_DATE");
	}
	elseif ((@$_GET['sort'] == 'code')&&($_GET['stype'] == 1)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY OWNER");

	}
	elseif ((@$_GET['sort'] == 'code')&&($_GET['stype'] == 2)){
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY OWNER DESC");
	}
	else{
		$usr = $conn->query("SELECT * FROM SP_USER ORDER BY USER_ID");
	}
	while($data_usr = $usr->fetch( PDO::FETCH_ASSOC )){
		// $iu = $i + 1;
		$u_id = $data_usr['USER_ID'];
		$u_name = trim($data_usr['LOGIN']);
		$uname = trim($data_usr['DESCR']);
		if ($uname == "") $uname = '-';
		$kolvo = $data_usr['COUNTER'];
		$lastdate = substr($data_usr['LAST_DATE'],8,2).".".substr($data_usr['LAST_DATE'],5,2).".".substr($data_usr['LAST_DATE'],0,4)." ".substr($data_usr['LAST_DATE'],11,8);
		$owner = $data_usr['OWNER'];
		$lctn_u = $data_usr['LCTN'];
		$type = $data_usr['ROLE'];
		echo"<tr><td>$u_id</td><td><input type='checkbox' id='chb".$u_id."' name='Arrus[]' value=".$u_id." class='form-check-input'></td><td>$u_name</td><td>$uname</td><td>$kolvo</td><td>$lastdate</td><td>$owner</td><td>$lctn_u</td><td>$type</td></tr>";
		
	}
?>
	</table>
	<div class="form-group text-center mt-2">
		<input type="button" class="btn btn-outline-success" value="Добавить пользователя" onclick="Addall(2)"/>
		<input type="button" class="btn btn-outline-dark" value="Редактировать пользователя" onclick="Editall(2)"/>
		<input type="button" class="btn btn-outline-danger" value="Удалить отмеченные" onclick="Remall(2)"/>
	</div>
<?php
}
?>
</form>
</div>