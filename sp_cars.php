<div class="container-fluid">
	<div class=" h4 my-3">Справочник автомобилей</div>
	<form id="f1" name="mform" method="post">
<?
if (isset($_GET['del'])){
	include "del_cars.php";
}
else{
if (isset($_GET['add'])){
	include "add_cars.php";
}
else{
if (isset($_GET['edit'])){
	include "edit_cars.php";
}
else{
?>

	<div class="form-group text-center">
		<input type="button" class="btn btn-outline-success" value="Добавить автомобиль" onclick="Addall(3)"/>
	</div>
	<table id="tpcars" class="table table-striped text-center">
		<thead>
			<tr>
				<th scope="col">Номер</th>
				<th scope="col">Местоположение</th>
				<th scope="col">Машина</th>
				<th scope="col">Перевозчик</th>
				<th scope="col">Водитель</th>
				<th scope="col">№ телефона</th>
				<th scope="col">Холод. установка</th>
				<th scope="col">Отметить</th>
			</tr>
		</thead>
<?
	$p_id = 0;
	$sp_auto = $conn->query("SELECT * FROM SP_CARS ORDER BY ANUM");
	while($data_auto = $sp_auto->fetch( PDO::FETCH_ASSOC )){
		$sp_anum = $data_auto['ANUM'];
		$sp_lctn = $data_auto['LCTN'];
		$name_lctn = @$conn->query("SELECT * FROM SP_LCTN WHERE LCTN = ".$sp_lctn);
		while($data_nm = @$name_lctn->fetch( PDO::FETCH_ASSOC )){
			$nlctn = $data_nm['DSCR'];
		}
		$sp_nazv = trim($data_auto['AUTO']);
		if ($sp_nazv == "") $sp_nazv = "-";	
		$sp_own = $data_auto['OWNER'];
		$name_owner = @$conn->query("SELECT * FROM SP_OWNER WHERE OWNER = ".$sp_own);
		while ($data_owner = @$name_owner->fetch( PDO::FETCH_ASSOC )){
			$nowner = $data_owner['NAME'];
		}
		$sp_driver = $data_auto['DRIVER'];
		if ($sp_driver == "") $sp_driver = "-";
		$sp_tel = $data_auto['TELDRV'];
		if ($sp_tel == "") $sp_tel = "-";
		$sp_hol = $data_auto['HOLOD'];
		if ($sp_hol == "") $sp_hol = "-";
		echo"<tr><td>$sp_anum</td><td>$nlctn</td><td>$sp_nazv</td><td>$nowner</td><td>$sp_driver</td><td>$sp_tel</td><td>$sp_hol</td><td><input type='checkbox' id='chb".$p_id."' name='delp[]' value=".$p_id." class='form-check-input'></td></tr>";
		$p_id++;
	}
?>
	</table>
	<div class="text-center my-3">
		<!--
		<input type="button" class="linp" id="admbutns" value="Удалить отмеченные" onclick="Remall(1)"/>
		<input type="button" class="linp" id="admbutns" value="Редактировать перевозчика" onclick="Editall(1)"/></center>
		-->
		<input type="button" class="btn btn-outline-success" value="Добавить автомобиль" onclick="Addall(3)"/>
	</div>
<?
}
}
}
?>
</form>
</div>