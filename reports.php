<div id="progress"></div>
<div id="all" class="container-fluid">
	<div class="row">
		<div class="col-md-4">
<?
$num_rep = intval($_GET['rep']);
/*
if (($_GET['rep'] == 1)||($_GET['rep'] == 4)||($_GET['rep'] == 5)||($_GET['rep'] == 9)){
?>
	<div class="h4 my-3">
		Настройки <?if ($_GET['rep'] == 1){?>отчёта по текущему местоположению машин<?}
		else{
		if ($_GET['rep'] == 4){?>сводного отчёта<?}
		else{?>отчёта по приборам<?}}?>
	</div>
	<div id="reppad">
<?
if (($_GET['rep'] == 1)||($_GET['rep'] == 4)){
?>
	Выбор региона:<br/>
	<select name="smap" id="gidmap" size="1" class="lsel" onchange="delinfo()">
	<option value="">Выбрать</option>
<?
		$m=@mysql_query("SELECT `maps`.`rusname`, `maps`.`map` FROM `maps`, `users` WHERE `users`.`u_name` = '".$user."' AND FIND_IN_SET(`maps`.`lctn`,`users`.`lctn`) ORDER BY `maps`.`rusname`");
		echo mysql_error();
		for ($i=0; $i<mysql_num_rows($m); $i++){
			$resm=mysql_fetch_array($m);
			echo"<option value='$resm[map]'>$resm[rusname]</option>";
		}
?>
	</select>
<?
}
if (($_GET['rep'] == 4)||($_GET['rep'] == 5)){
?>
			<div id="razd"></div>
<?
	include 'date.htm';
?>
	<div id="razd"></div>
<?
}
?>
if ($_GET['rep'] == 4){
?>
<!--
Минимальное время движения на отрезке, в секундах:<input class="constrep" type="text" id="mintrep" name="mintrep" value="<?if (isset($_POST['mintrep'])){echo $_POST['mintrep'];?><?}else{?>60<?}?>" size="3" maxlength="3" /><br/><br/>
Минимальное расстояние отрезка движения, в метрах:<input class="constrep" type="text" id="minlrep" name="minlrep" value="<?if (isset($_POST['minlrep'])){echo $_POST['minlrep'];?><?}else{?>50<?}?>" size="3" maxlength="3"/>
-->
<?
}

?>
	</div>
<?
}
*/
$var_rep2 = array(2,3,6,7,8);

if (in_array($num_rep, $var_rep2)){
	switch ($num_rep) {
    case 2:
        $part_z = "отчёта по интервалам движения";
        break;
    case 3:
        $part_z = "отчёта по стоянкам";
        break;
    case 6:
        $part_z = "отчёта по стоянкам со снимками карт";
        break;
    case 7:
        $part_z = "отчёта по стоянкам с координатами";
        break;
    case 8:
        $part_z = "сравнительного отчёта";
        break;
}
?>
	<div class="h4 my-3">
		Настройки <?= $part_z; ?>
	</div>
	<div class="form-group">
		<label class="h6">Выбор машины:</label>
		<? include 'selcars.php'; ?>
	</div>
	<div class="form-group">
		<? include 'date.htm'; ?>
	</div>
<?
}
?>
			<div class="form-check">
				<input type="radio" name="rtype" id="rtype1" class="form-check-input" checked="checked"/>
				<label for="rtype1" class="form-check-label rlabel"> - отчёт в формате html</label>
			</div>
			<div class="form-check ">
				<input type="radio" name="rtype" id="rtype2" class="form-check-input"/>
				<label for="rtype2" class="form-check-label rlabel"> - отчёт в формате pdf (на печать)</label>
			</div>
			<div class="form-check form-group">
				<input type="radio" name="rtype" id="rtype3" class="form-check-input"/>
				<label for="rtype3" class="form-check-label rlabel"> - отчёт в формате xls</label>
			</div>
			<div class="form-group">
				<input class="btn btn-secondary" type="button" value="Сформировать отчёт" onclick="frep<?= $num_rep ?>()"/>
			</div>
			<div id="result"></div>
		</div>
	</div>
</div>