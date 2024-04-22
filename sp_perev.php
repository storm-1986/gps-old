<div class="container-fluid">
	<div class=" h4 my-3">Справочник перевозчиков</div>
	<form id="f1" name="mform" method="post">
<?php
if (isset($_GET['del'])){
	include "del_perev.php";
}
elseif (isset($_GET['add'])){
	include "add_perev.php";
}
elseif (isset($_GET['edit'])){
	include "edit_perev.php";
}
else{
?>
	<div class="form-group text-center">
		<!--
		<input type="button" class="linp" id="admbutns" value="Удалить отмеченные" onclick="Remall(1)"/>
		<input type="button" class="linp" id="admbutns" value="Редактировать перевозчика" onclick="Editall(1)"/></center>
		-->
		<input type="button" class="btn btn-outline-success" value="Добавить перевозчика" onclick="Addall(1)"/>
	</div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th scope="col">Код</th>
				<th scope="col">Перевозчик</th>
				<th scope="col">Отметить</th>
			</tr>
		</thead>
<?
	$sp_per = $conn->query("SELECT * FROM SP_OWNER ORDER BY OWNER");
	while($data_per = $sp_per->fetch( PDO::FETCH_ASSOC )){
		$p_id = $data_per['OWNER'];
		$p_name = trim($data_per['NAME']);
		echo"<tr><td>$p_id</td><td>$p_name</td><td><input type='checkbox' id='chb".$p_id."' name='delp[]' value=".$p_id." class='rlabel'></td></tr>";
	}
?>
	</table>
	<div class="form-group text-center">
		<!--
		<input type="button" class="linp" id="admbutns" value="Удалить отмеченные" onclick="Remall(1)"/>
		<input type="button" class="linp" id="admbutns" value="Редактировать перевозчика" onclick="Editall(1)"/></center>
		-->
		<input type="button" class="btn btn-outline-success" value="Добавить перевозчика" onclick="Addall(1)"/>
	</div>
<?
}
?>
</form>
</div>