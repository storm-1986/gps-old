<?php
if ($_GET['ponline'] == 1){
	$zagl = 'Автомобили с данными за текущие сутки';
}elseif ($_GET['ponline'] == 2){
	$zagl = 'Автомобили, от которых нет данных за текущие сутки';
}
?>
<div class="container-fluid">
	<div class="row">
		<div class="h4 m-3"><?php echo $zagl;?></div>
	</div>
	<div class="row">
		<div class="col-md-12">
		<form action="#" method="post" id="filter_form">
			<div class="form-group">
				<div class="form-check form-check-inline">
					<input type="checkbox" name="mash" id="mash" class="form-check-input rlabel" <?php if (!isset($_POST['filter']) || isset($_POST['mash'])) echo 'checked="checked"';?>/><label for="mash" class="form-check-label rlabel">Машины</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="checkbox" name="mvoz" id="mvoz" class="form-check-input rlabel" <?php if (!isset($_POST['filter']) || isset($_POST['mvoz'])) echo 'checked="checked"';?>/><label for="mvoz" class="form-check-label rlabel">Молоковозы</label>
				</div>
			</div>
			<div class="form-group">
<?php
				include_once "data_listauto.php";
				echo $ins_lctn;
?>				
			</div>
<?php
	if ($_SESSION['userowner'] == 0){
?>
			<div class="form-group">
				<div class="form-check form-check-inline">
					<input type="checkbox" name="sav" id="sav" class="form-check-input rlabel" <?php if (isset($_POST['sav'])) echo 'checked="checked"';?>/><label for="sav" class="form-check-label rlabel">Только машины Савушкин продукт</label>
				</div>
			</div>
<?php
	}
?>
			<input type="hidden" name="filter" value="1">
			<div class="form-group">
				<input type="button" value="Применить фильтр" id="start" class="btn btn-outline-success"/>
			</div>
		</form>
		</div>
	</div>
	<div class="row">
		<table class="table table-striped tbl-nodata">
  			<thead>
    			<tr>
      				<th scope="col">#</th>
      				<th scope="col">Номер машины</th>
      				<th scope="col">Владелец</th>
      				<th scope="col">Время получения последних данных</th>
      				<th scope="col">Последние координаты</th>
      				<th scope="col">Последняя скорость (км/ч)</th>
    			</tr>
  			</thead>
  			<tbody id="nodata_body">
<?php
				echo $ins;
?>
			</tbody>
		</table>
	</div>
</div>