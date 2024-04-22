<?
if (isset($_POST["delp"])){
	$pdel = $_POST["delp"];		
	$pq = "DELETE FROM SP_OWNER WHERE";
	foreach($pdel as $k=>$v) $pq=$pq." (OWNER = $v) or ";
	$pq = substr($pq,0,strlen($pq)-4);
	$pr= ads_do($rConn, $pq);
?>
<center>
	<div class="note">Данные успешно удалены</div>
	<input type="button" value="К списку перевозчиков" class="linp"  id="admbutns" onclick="Back(1)"/>
</center>
<?
}
else{
?>
<center>
	<div class="note">Вы не выбрали ни одной записи</div>
	<input type="button" value="К списку перевозчиков" class="linp"  id="admbutns" onclick="Back(1)"/>
</center>
<?
}
?>