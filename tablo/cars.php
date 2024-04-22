<?

include_once "options.php";

$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ( $rConn == False ){
	$strErrCode = ads_error( );
	$strErrString = ads_errormsg( );
	echo "<div id='spis'>Ошибка соединения с базой данных.</div>\n";
	exit;
}

$day = date("d");
$mes = date("m");
$year = date("Y");
$our = date("H");
$min = date("i");
$sec = date("s");

?>
<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="Refresh" content="120"/>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/cars_script.js?k=15"></script>
<link rel="stylesheet" type="text/css" href="css/cars_design.css?n=<?echo mt_rand();?>"/>
</head>
<body onload="set_tr_color()">
<div id="all">
<form id="mform" method="GET">
<div id="ctrlfiltr" onclick="shfiltr()">Раскрыть фильтры</div>
<div id="filtr">
<div class="fstatus">
	<div>Тип машины</div>
	<div><input type="checkbox" name="mol" id="mol" value="1" <?if (isset($_GET['mol'])) echo "checked=\"checked\""?>/><label for="mol">Молоковозы</label></div>
	<div><input type="checkbox" name="gryz" id="gryz" value="0" <?if (isset($_GET['gryz'])) echo "checked=\"checked\""?>/><label for="gryz">Грузовые</label></div>
<!--
	<div><input type="checkbox" name="export" id="export" value="2" <?if (isset($_GET['export'])) echo "checked=\"checked\""?>/><label for="export">Экспорт</label></div>
-->
</div>
<div class="fstatus">
	<div>Статус</div>
	
	<div><input type="checkbox" name="st_ok" id="st_ok" value="ok" <?if (isset($_GET['st_ok'])) echo "checked=\"checked\""?>/><label for="st_ok">Включая машины с ошибками</label></div>
	<div><input type="checkbox" name="st_er" id="st_er" value="err" <?if (isset($_GET['st_er'])) echo "checked=\"checked\""?>/><label for="st_er">Только машины с ошибками</label></div>

</div>
<div class="fstatus">
	<div>Скорость</div>
	<div><input type="checkbox" name="skor0" id="skor0" value="0" <?if (isset($_GET['skor0'])) echo "checked=\"checked\""?>/><label for="skor0">0 км/ч</label></div>
	<div><input type="checkbox" name="skor1-30" id="skor1-30" value="1" <?if (isset($_GET['skor1-30'])) echo "checked=\"checked\""?>/><label for="skor1-30">1 - 30 км/ч</label></div>
	<div><input type="checkbox" name="skor30-70" id="skor30-70" value="2" <?if (isset($_GET['skor30-70'])) echo "checked=\"checked\""?>/><label for="skor30-70">31 - 70 км/ч</label></div>
	<div><input type="checkbox" name="skor70-100" id="skor70-100" value="3" <?if (isset($_GET['skor70-100'])) echo "checked=\"checked\""?>/><label for="skor70-100">71 - 100 км/ч</label></div>
	<div><input type="checkbox" name="skor101" id="skor101" value="4" <?if (isset($_GET['skor101'])) echo "checked=\"checked\""?>/><label for="skor101">> 100 км/ч</label></div>
</div>
<div class="fstatus">
	<div>Филиалы</div>
<?
$qfil = ads_do($rConn, "SELECT * FROM SP_LCTN");
$count_fil = 0;
while (ads_fetch_row($qfil)){
	$fl_chbx = "";
	$nmfil = ads_result($qfil, "DSCR");
	$nmfil = iconv("CP1251", "UTF-8", $nmfil);
	$codefil = ads_result($qfil, "LCTN");
	if (isset($_GET['fil'.$count_fil])) $fl_chbx = "checked=\"checked\"";
	echo"<div><input type=\"checkbox\" name=\"fil$count_fil\" class=\"inp_filial\" id=\"fil$count_fil\" value=\"$codefil\" $fl_chbx><label for=\"fil$count_fil\">$nmfil</label></div>";
	$count_fil++;
}
?>
</div>
<input id="filtr_btn" type="button" value="Применить" onclick="setfiltres()"/>
</div>
<table id="mtbl">
<tr>
<?
$qsort = "";
if ($_GET['sort'] == 'auto'){
	if ($_GET['sort1_type'] == 0){
		$sort1_type = 1;
		$qsort = ' ORDER BY ANUM';
	}else{
		$sort1_type = 0;
		$qsort = ' ORDER BY ANUM DESC';
	}
}else{
	$sort1_type = 0;
}
if ($_GET['sort'] == 'status'){
	if ($_GET['sort2_type'] == 0){
		$sort2_type = 1;
		$qsort = ' ORDER BY STATUS';
	}else{
		$sort2_type = 0;
		$qsort = ' ORDER BY STATUS DESC';
	}
}else{
	$sort2_type = 0;
}
if ($_GET['sort'] == 'filial'){
	if ($_GET['sort3_type'] == 0){
		$sort3_type = 1;
		$qsort = ' ORDER BY LCTN';
	}else{
		$sort3_type = 0;
		$qsort = ' ORDER BY LCTN DESC';
	}
}else{
	$sort3_type = 0;
}
if ($_GET['sort'] == 'owner'){
	if ($_GET['sort4_type'] == 0){
		$sort4_type = 1;
		$qsort = ' ORDER BY OWNER';
	}else{
		$sort4_type = 0;
		$qsort = ' ORDER BY OWNER DESC';
	}
}else{
	$sort4_type = 0;
}
if ($_GET['sort'] == 'plan_tr'){
	if ($_GET['sort5_type'] == 0){
		$sort5_type = 1;
		$qsort = ' ORDER BY DTPLANB';
	}else{
		$sort5_type = 0;
		$qsort = ' ORDER BY DTPLANB DESC';
	}
}else{
	$sort5_type = 0;
}
if ($_GET['sort'] == 'nedo'){
	if ($_GET['sort6_type'] == 0){
		$sort6_type = 1;
		$qsort = ' ORDER BY NEDOVOZ';
	}else{
		$sort6_type = 0;
		$qsort = ' ORDER BY NEDOVOZ DESC';
	}
}else{
	$sort6_type = 0;
}
?>
	<th>
		<input type="hidden" name="sort" id="sort" value=""/>
		<input type="hidden" name="sort1_type" id="sort1_type" value="<?echo $sort1_type;?>"/>
		<span class="atd" onclick="sort('auto')">Машина</span>
	</th>
	<th>
		<input type="hidden" name="sort2_type" id="sort2_type" value="<?echo $sort2_type;?>"/>
		<span class="atd" onclick="sort('status')">Статус</span>
	</th>
	<th>
		<input type="hidden" name="sort5_type" id="sort5_type" value="<?echo $sort5_type;?>"/>
		<span class="atd" onclick="sort('plan_tr')">План. маршрут</span>
	</th>
	<th>
		Факт. маршрут
	</th>
	<th><input type="hidden" name="sort6_type" id="sort6_type" value="<?echo $sort6_type;?>"/>
		<span class="atd" onclick="sort('nedo')">Выполн</span>
	</th>
	<th>Адрес</th>
	<th>Скор</th>
	<th>
		<input type="hidden" name="sort3_type" id="sort3_type" value="<?echo $sort3_type;?>"/>
		<span class="atd" onclick="sort('filial')">Филиал</span>
	</th>
	<th>
		<input type="hidden" name="sort4_type" id="sort4_type" value="<?echo $sort4_type;?>"/>
		<span class="atd" onclick="sort('owner')">Владелец</span>
	</th>
</tr>
<?
$all_filtrs = "";
$all_filtrs1 = "";
$all_filtrs2 = "";
$all_filtrs3 = "";
$all_filtrs4 = "";
$fff = 0;
foreach ($_GET as $k => $v){
	if ($k == 'mol')	$filtr4 = "ATYPE = 1";
    if ($k == 'gryz') 	$filtr4 = "ATYPE = 0";
    if ($k == 'export') $filtr4 = "ATYPE = 5";

	if ($k == 'st_ok')	$filtr1 = "STATUS > 0";
    if ($k == 'st_er') 	$filtr1 = "STATUS > 100";
	    
    if ($k == 'skor0')	$filtr2 = "VEL < 3";
    if ($k == 'skor1-30')	$filtr2 = "(VEL >= 3 AND VEL < 30)";
    if ($k == 'skor30-70')	$filtr2 = "(VEL >= 30 AND VEL < 70)";
    if ($k == 'skor70-100')	$filtr2 = "(VEL >= 70 AND VEL < 100)";
    if ($k == 'skor101')	$filtr2 = "(VEL >= 100)";

    if (substr($k, 0, 3) == "fil") $filtr3 = "LCTN = ".substr($k, 3);

	if ($k == 'mol'||$k == 'gryz'||$k == 'export'){
    if ($all_filtrs4 == ""){
    	$all_filtrs4 = "(".$filtr4.") AND";
    }else{
    	$all_filtrs4 = substr($all_filtrs4, 0, -5);
    	$all_filtrs4 .= " OR ".$filtr4.") AND";
    }
    }

	if ($k == 'st_ok'||$k == 'st_er'){
    if ($all_filtrs1 == ""){
    	$all_filtrs1 = "(".$filtr1.") AND";
    }else{
    	$all_filtrs1 = substr($all_filtrs1, 0, -5);
    	$all_filtrs1 .= " OR ".$filtr1.") AND";
    }
    }
    if (substr($k, 0, 4) == "skor"){
    	if ($all_filtrs2 == ""){
    		$all_filtrs2 = "(".$filtr2.") AND";
    	}else{
    		$all_filtrs2 = substr($all_filtrs2, 0, -5);
			$all_filtrs2 .= " OR ".$filtr2.") AND";
    	}
    }
    if (substr($k, 0, 3) == "fil"){
    	if ($all_filtrs3 == ""){
    		$all_filtrs3 = " (".$filtr3.") AND";
    	}else{
    		$all_filtrs3 = substr($all_filtrs3, 0, -5);
    		$all_filtrs3 .= " OR ".$filtr3.") AND";
    	}
    }
}
if ($all_filtrs1 == ""){
	$all_filtrs1 = "STATUS < 101 AND";
}

if ($all_filtrs1 !== "" || $all_filtrs2 !== "" || $all_filtrs3 !== "" || $all_filtrs4 !== ""){
	$all_filtrs = " WHERE ".$all_filtrs1.$all_filtrs2.$all_filtrs3.$all_filtrs4;
	$all_filtrs = substr($all_filtrs, 0, -4);
}

//echo "SELECT * FROM BD_TABLO".$all_filtrs.$qsort;

$qauto = ads_do($rConn, "SELECT * FROM BD_TABLO".$all_filtrs.$qsort."");
$count = 0;
while (ads_fetch_row($qauto)){
	$count++;
	if ($count%2 == 0){
		$grtr = '';
	}else{
		$grtr = ' class="grtr"';
	}
	$anum = trim(iconv("CP1251", "UTF-8", ads_result($qauto, "ANUM")));
	$dt_beg = ads_result($qauto, "DTBEG");
	$stoyanki = ads_result($qauto, "DELIVORD");
	$stat = ads_result($qauto, "STATUS");
	$dt_bplan = ads_result($qauto, "DTPLANB");
	if ($stat == 200){
		$dt_plan = "-<br>-";
	}else{
		$dt_eplan = ads_result($qauto, "DTPLANE");
		$dt_bplan = substr($dt_bplan,8,2).".".substr($dt_bplan,5,2).".".substr($dt_bplan,2,2)."&nbsp;".substr($dt_bplan,11,5);
		$dt_eplan = substr($dt_eplan,8,2).".".substr($dt_eplan,5,2).".".substr($dt_eplan,2,2)."&nbsp;".substr($dt_eplan,11,5);
		$dt_plan = $dt_bplan."<br>".$dt_eplan;
	}
	if ($stat > 100){
		$stat = ads_result($qauto, "ERROR");
		$stat = iconv("CP1251", "UTF-8", $stat);
	}else{
		$stat = 'ОК';
	}
	$dt_bfact = ads_result($qauto, "DTFACTB");
	$dt_efact = ads_result($qauto, "DTFACTE");
	if ($dt_bfact == ""){
		$dt_bfact = "-";
	}else{
		$dt_bfact = substr($dt_bfact,8,2).".".substr($dt_bfact,5,2).".".substr($dt_bfact,2,2)."&nbsp;".substr($dt_bfact,11,5);
	}
	if ($dt_efact == ""){
		$dt_efact = "-";
	}else{
		$dt_efact = substr($dt_efact,8,2).".".substr($dt_efact,5,2).".".substr($dt_efact,2,2)."&nbsp;".substr($dt_efact,11,5);
	}
	$dt_fact = $dt_bfact."<br>".$dt_efact;
	$ned = ads_result($qauto, "NEDOVOZ");
	if ($ned == "") $ned = "-";
	$pplan = ads_result($qauto, "FPLAN");
	if ($pplan == "0"){
		$nedoplan = "-";
	}else{
		$nedoplan = $pplan - $ned."/".$pplan;
	}
	$skor = intval(ads_result($qauto, "VEL"));
	$adr = ads_result($qauto, "ADDR");
	$adr = iconv("CP1251", "UTF-8", $adr);
	$lctn = ads_result($qauto, "LCTN");
	$qlctn = ads_do($rConn, "SELECT * FROM SP_LCTN WHERE LCTN = $lctn");
	$nmlctn = ads_result($qlctn, "DSCR");
	$nmlctn = iconv("CP1251", "UTF-8", $nmlctn);
	$codeown = ads_result($qauto, "OWNER");
	$qown = ads_do($rConn, "SELECT * FROM SP_OWNER WHERE OWNER = $codeown");
	$owner = ads_result($qown, "NAME");
	$owner = iconv("CP1251", "UTF-8", $owner);
	echo "<tr".$grtr." id=".$anum." onclick=\"track('$anum','$dt_beg','$stoyanki')\"><td id=\"$anum\" class=\"atd\">$anum</td><td>$stat</td><td>$dt_plan</td><td>$dt_fact</td><td>$nedoplan</td><td>$adr</td><td>$skor</td><td>$nmlctn</td><td>$owner</td></tr>";
}
?>
</table>
<div class="link_to_prop"><input type="button" value="Настройки отображения" onclick="parent.location.href='index.php?exit=1'"/></div>
<?
//print_r($_GET);
ads_close($rConn);
?>
</form>
</div>
</body>
</html>