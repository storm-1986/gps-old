<?php
// Добавление машины для трекеров
if (isset($_GET['a_number'])){

	include_once "options.php";

	$new_cnumber = strtoupper($_GET['a_number']);
	$new_clctn = $_GET['a_lctn'];
	$new_cperev = $_GET['a_perev'];
	$new_cname = urldecode($_GET['a_carname']);
	$new_cdriver = urldecode($_GET['a_driver']);
	$new_ctel = urldecode($_GET['a_tel']);
	$new_chol = urldecode($_GET['a_hol']);
	
	$anum_test = $conn->query("SELECT * FROM SP_CARS WHERE ANUM = '".$new_cnumber."'");
	$i = 0;
	while($data_test = $anum_test->fetch( PDO::FETCH_ASSOC )){
		$i++;
	}
	if($i > 0){
		$result = array('type'=>'error','msg'=>'Введённый вами номер автомобиля '.$new_cnumber.' уже есть в базе');
	}else{
		$inscar = $conn->query("INSERT INTO SP_CARS VALUES ('$new_cnumber',$new_clctn,'$new_cname',$new_cperev,'$new_cdriver','$new_ctel','$new_chol',0)");
		if($inscar === false){
			$result = array('type'=>'error','msg'=>'Ошибка добавления данных');
		}else{
			$result = array('type'=>'success','msg'=>'Автомобиль '.$new_cnumber.' успешно добавлен в базу');
		}
	}
	print json_encode($result);
}else{
// Добавление машины для админки
if (isset($_GET['add'])&&isset($_GET['addcarproc'])){
	$n_anum = strtoupper($_POST['n_anum']);
	$anum_prov = $conn->query("SELECT * FROM SP_CARS WHERE ANUM = '".$n_anum."'");
	$prov_i = 0;
	while($data_prov = $anum_prov->fetch( PDO::FETCH_ASSOC )){
		$prov_i++;
	}
	if($prov_i == 0){
		$n_lctn = intval($_POST['n_carlctn']);
		$n_aname = $_POST['n_aname'];
		$n_perev = intval($_POST['perev']);
		$n_driver = $_POST['n_driver'];
		$n_tel = $_POST['n_tel'];
		$n_holod = $_POST['n_holod'];
		$insper = $conn->query("INSERT INTO SP_CARS VALUES ('$n_anum',$n_lctn,'$n_aname',$n_perev,'$n_driver','$n_tel','$n_holod',0)");
		if ($insper === true) $mess_add_car = "Данные успешно добавлены.";
	}
	else{
		$mess_add_car = "Введённый вами номер машины уже существует. Повторите ввод.";
	}
?>
		<div class="text-center my-3">
			<div class="h5 mb-4"><?= $mess_add_car; ?></div>
			<input type="button" class="btn btn-outline-success" value="К вводу новых данных" onclick="Backtoadd(3)"/>
			<input type="button" class="btn btn-outline-danger" value="К списку машин" onclick="Back(3)"/>
		</div>
<?php
}
else{	// форма для добавления машины
?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th scope="col">Номер машины<span style="color: red;"> (*)</span></th>
				<th scope="col">Местоположение<span style="color: red;"> (*)</span></th>
				<th scope="col">Машина</th>
				<th scope="col">Перевозчик<span style="color: red;"> (*)</span></th>
				<th scope="col">Водитель</th>
				<th scope="col">№ телефона</th>
				<th scope="col">Холод. установка</th>
			</tr>
		</thead>
		<tr>
			<td>
				<input type="text" name="n_anum" maxlength="10" id="n_anum" class="form-control" />
			</td>
			<td>
				<select name="n_carlctn" size="1" id="n_carlctn" class="form-control" >
					<option value="">Выбрать</option>
<?php
					$qnlctn = "SELECT * FROM SP_LCTN ORDER BY DSCR";
					$resqnlctn = $conn->query($qnlctn);
					while($data_lctn = $resqnlctn->fetch( PDO::FETCH_ASSOC )){
						$s_clctn = $data_lctn['LCTN'];
						$s_nlctn = $data_lctn['DSCR'];
						echo"<option value=$s_clctn>$s_nlctn</option>";
					}
?>
				</select>
			</td>
			<td>
				<input type="text" name="n_aname" maxlength="16" id="n_aname" class="form-control"/>
			</td>
			<td class="text-center">
				<div class="form-group">
				<select name="perev" size="1" id="perev" class="form-control">
					<option value="">Выбрать</option>
<?php
					$qper = "SELECT * FROM SP_OWNER ORDER BY NAME";
					$resqper = $conn->query($qper);
					while($data_per = $resqper->fetch( PDO::FETCH_ASSOC )){
						$tr_idper = $data_per['OWNER'];
						$tr_per = $data_per['NAME'];
						echo"<option value=$tr_idper>$tr_per</option>";
					}
?>
				</select>
				</div>
				<a href="index.php?admin=1" target="_blank" class="btn btn-outline-dark" role="button">Добавить перевозчика</a>
			</td>
			<td>
				<input type="text" name="n_driver" maxlength="32" id="n_driver" class="form-control" />
			</td>
			<td>
				<input type="text" name="n_tel" maxlength="13" id="n_tel" class="form-control" />
			</td>
			<td>
				<input type="text" name="n_holod" maxlength="16" id="n_holod" class="form-control" />
			</td>
		</tr>
	</table>
	<div class="form-group mt-2">
		Поля, отмеченные <span style="color: red;"> (*)</span>, обязательны для заполнения.
	</div>
	<div class="form-group text-center mt-2">
		<input type="button" value="Добавить машину" class="btn btn-outline-success" onclick="Addcarproc()"/>
		<input type="button" value="Назад" class="btn btn-outline-danger" onclick="Back(3)"/>
	</div>
<?php
}
}
?>