<div class="text-center my-3">
<?php
if (isset($_POST['Arrus'])){
	$udel = $_POST["Arrus"];		
	$q = "DELETE FROM SP_USER WHERE";
	foreach($udel as $k => $v) $q = $q." (USER_ID = $v) OR ";
	$q = substr($q, 0, strlen($q) - 4);
	$usr = $conn->query($q);
?>
	<div class="h5 mb-4">Данные успешно удалены.</div>
<?php
}
else{
?>
	<div class="h5 mb-4">Вы не выбрали ни одной записи</div>
<?php
}
?>
	<input type="button" class="btn btn-outline-danger" value="К списку пользователей" onclick="Back(2)"/>
</div>