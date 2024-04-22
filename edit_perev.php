<?
if (isset($_GET['edit'])&&isset($_GET['editperproc'])){
	$inspcode = intval($_POST['edpcode']);
	$oldpcode = intval($_POST['editpid']);
	$eper_prov = ads_do($rConn, "SELECT * FROM SP_OWNER WHERE OWNER = ".$inspcode);
	$i = 0;
	while (ads_fetch_row($eper_prov)){
		$i++;
	}
	if($i == 0 || $inspcode == $oldpcode){
		$nedperev = str_replace("'",'"', $_POST[edpname]);
		$nedperev = iconv("UTF-8", "CP1251", $nedperev);
		$insedit = ads_do($rConn, "UPDATE SP_OWNER SET OWNER = $inspcode, NAME = '$nedperev' WHERE OWNER = $oldpcode");
?>
	<center>
		<div class="note">Данные успешно изменены.</div>
		<input type="button" value="К списку перевозчиков" class="linp"  id="admbutns" onclick="Back(1)"/>
	</center>
<?
	}
	else{
?>
		<center>
			<div class="note">Введённый вами код перевозчика уже существует. Повторите ввод.</div>
			<input type="button" value="Отмена" class="linp"  id="admbutns" onclick="Back(1)"/>
		</center>
<?		
	}
}
else{
if (isset($_POST['delp'])){
	$pedit = $_POST["delp"];
	$edpq = "SELECT * FROM SP_OWNER WHERE";
	foreach($pedit as $k=>$v) $edpq=$edpq." (OWNER = $v) or ";
	$edpq = substr($edpq,0,strlen($edpq)-4);
	$edpr = ads_do($rConn, $edpq);
	while (ads_fetch_row($edpr)){
		$p_code = ads_result($edpr, "OWNER");
		$p_name = trim(ads_result($edpr, "NAME"));
		$p_name = stripcslashes($p_name);
		$p_name = iconv("CP1251", "UTF-8", $p_name);
	}
?>
	<input type="hidden" name="editpid" value="<?echo $p_code?>"/>
	<div style="border-top: 1px solid black; border-bottom: 1px solid black;">
	<table id="tpcars" cellspacing='0' cellpadding='0'>
		<tr class="tptr3"><td class="tdol1">Код перевозчика</td><td class="tdol1">Перевозчик</td></tr>
		<tr class="tptr2">
			<td class="tdol1" width="50px"><input type="text" name="edpcode" maxlength="3" id="edpcode" value="<?echo $p_code?>" style="width: 30px;"/></td>
			<td class="tdol1"><input type="text" name="edpname" maxlength="30" id="edpname" value="<?echo $p_name?>" style="width: 300px;"/></td>
		</tr>
	</table>
	</div>
	<center>
		<input type="button" value="Сохранить изменения" class="linp" id="admbutns" onclick="Editperproc()"/><input type="button" value="Отмена" class="linp" id="admbutns" onclick="Back(1)"/>
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
}
?>