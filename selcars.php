<?
if ($_SESSION['usertype'] == 1000000000){
	$q_nc = "SELECT ANUM FROM SP_CARS WHERE PF <> 3 AND LCTN IN (".$_SESSION['lctn'].") ORDER BY ANUM";
}else{
	if($_SESSION['usertype'] == 20000000){
		if (isset($_GET['add'])){ // для добавления трекеров
			$q_nc = "SELECT ANUM FROM SP_CARS WHERE PF = 2 ORDER BY ANUM";
		}else{
			$q_nc = "SELECT ANUM FROM SP_CARS WHERE PF = 2 AND LCTN IN (".$_SESSION['lctn'].") ORDER BY ANUM";
		}
	}
	else{
		if ($_SESSION['userowner'] == 0){
			$q_nc = "SELECT ANUM FROM SP_CARS WHERE PF <> 2 AND PF <> 3 AND LCTN IN (".$_SESSION['lctn'].") ORDER BY ANUM";
		}
		else{
			$q_nc = "SELECT ANUM FROM SP_CARS WHERE PF <> 2 AND PF <> 3 AND OWNER = ".$_SESSION['userowner']." AND LCTN IN (".$_SESSION['lctn'].") ORDER BY ANUM";
		}
	}
}

$nc = $conn->query($q_nc);

$list_cars = "";
while($data_cars = $nc->fetch( PDO::FETCH_ASSOC )){
	$resc = trim($data_cars["ANUM"]);
	$resc = iconv("CP1251","UTF-8",$resc);
	$list_cars .= "{text: '".$resc."', value: '".$resc."'},";
}
?>
<script type='text/javascript'>
	$(function() { $('#car_id').immybox({ choices: [<?echo $list_cars;?>]}); });
</script>
<input type="text" class="form-control" name="car_id" id="car_id" placeholder="Выбор машины" <?if (isset($_GET['rep'])) echo"onchange='delinfo()'";?>/>