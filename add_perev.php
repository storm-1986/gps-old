<?php
// Добавление перевозчика для трекеров
if (isset($_POST['perev'])){
	include_once "options.php";

	$new_perev = urldecode($_POST['perev']);
	$new_perev = trim($new_perev);
	$new_perev = str_replace("'",'"', $new_perev);
	$test_perev = $conn->query("SELECT * FROM SP_OWNER WHERE NAME = '$new_perev'");
	$i = 0;

	while($data_test = $test_perev->fetch( PDO::FETCH_ASSOC )){
		$i++;
	}
	if($i > 0){
		$result = array('type'=>'error','msg'=>'Введённый вами перевозчик '.$new_perev.' уже есть в базе');
	}else{
		$insper = $conn->query("INSERT INTO SP_OWNER (NAME) VALUES ('$new_perev')");
		if ($insper === false){
			$result = array('type'=>'error','msg'=>'Ошибка добавления данных');
		}else{
			$result = array('type'=>'success','msg'=>'Перевозчик '.$new_perev.' успешно добавлен в базу.');
		}
	}
print json_encode($result);
}else{

	// Добавление перевозчика для админки
	if (isset($_GET['add'])&&isset($_GET['addperproc'])){
		$nperev = str_replace("'",'"', $_POST['nperev']);
		$insper = $conn->query("INSERT INTO SP_OWNER (NAME) VALUES ('$nperev')");
	//print_r($_POST);
	?>
		<div class="text-center my-3">
			<div class="h5 mb-4">Данные успешно добавлены.</div>
			<input type="button" class="btn btn-outline-success" value="К вводу новых данных" onclick="Backtoadd(1)"/>
			<input type="button" class="btn btn-outline-danger" value="К списку перевозчиков" onclick="Back(1)"/>
		</div>
	<?php	
	}
	else{	// форма для добавления перевозчика
	?>
		<div class="text-center my-3">
			<div class="form-group">
				<input type="text" class="form-control" name="nperev" maxlength="30" id="nperev" placeholder="Перевозчик" />
			</div>
			<input type="button" class="btn btn-outline-success" value="Добавить перевозчика" onclick="Addperproc()"/>
			<input type="button" class="btn btn-outline-danger" value="К списку перевозчиков" onclick="Back(1)"/>
		</div>
	<?php
	}
}
?>