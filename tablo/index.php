<?
if ($_GET['exit'] == 1){
	setcookie('type','',time()+2678400);
	setcookie('w1','',time()+2678400);
	setcookie('w2','',time()+2678400);
	setcookie('w3','',time()+2678400);
	setcookie('w4','',time()+2678400);
	setcookie('id_tr','',time()+2678400);
	$nastr = 1;
}else{
	if(!isset($_POST['vybor'])&&!isset($_COOKIE['type'])){
/*
	print_r($_POST); echo " - POST<br>";
	print_r($_COOKIE); echo " - COOKIE<br>";
*/
		$nastr = 1;
	}else{
/*
	if (!isset($_POST['vybor'])){
	$nastr = 1;	
	}else{
*/

	$nastr = 0;
	if ($_COOKIE['type'] > ''){
		$type = $_COOKIE['type'];
	}else{
		$type = $_POST['vybor'];	
		setcookie('type',$type,time()+2678400);
	}
	if ($_COOKIE['w1'] > ''){
		$w1 = $_COOKIE['w1'];
	}else{
		$w1 = $_POST['sel1_'.$type];	
		setcookie('w1',$w1,time()+2678400);
	}
	if ($_COOKIE['w2'] > ''){
		$w2 = $_COOKIE['w2'];
	}else{
		$w2 = $_POST['sel2_'.$type];	
		setcookie('w2',$w2,time()+2678400);
	}
	if ($_COOKIE['w3'] > ''){
		$w3 = $_COOKIE['w3'];
	}else{
		$w3 = $_POST['sel3_'.$type];
		setcookie('w3',$w3,time()+2678400);
	}
	if ($_COOKIE['w4'] > ''){
		$w4 = $_COOKIE['w4'];
	}else{
		$w4 = $_POST['sel4_'.$type];
		setcookie('w4',$w4,time()+2678400);
	}
/*
}
*/
}
}
// http://10.150.15.4:8080/web/index.html?lang=ru - видеонаблюдение
if ($nastr == 1){
?>
<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>GPS control 2</title>
</head>
<body>
	<h1 style="text-align: center;">Настройка параметров</h1>
<div style="width: 450px; margin: 0 auto;">
	<div style="text-align: center;">Выберите нужный формат вывода страниц и сами страницы</div>
	<form id="seltype" method="post" action="index.php">
<!--	Для 2 экранов	-->
	<div class="var">
		<input type="radio" name="vybor" id="vybor1" value="1" style="float: left; margin: 18px 10px; cursor: pointer;"/>
		<table id="tbl_var1" border="1" cellpadding="10px" style="margin: 10px 0px;">
			<tr>
			<td><select name="sel1_1" id="sel1_1" style="cursor: pointer;"><?include "list1.php";?></select></td>
			<td><select name="sel2_1" id="sel2_1" style="cursor: pointer;"><?include "list2.php";?></select></td>
			</tr>
		</table>
	</div>
	<div class="var">
		<input type="radio" name="vybor" id="vybor2" value="2" style="clear: left; float: left; margin: 37px 10px; cursor: pointer;"/>
		<table id="tbl_var1" border="1" cellpadding="10px" style="margin-bottom: 10px;">
			<tr>
			<td><select name="sel1_2" id="sel1_2" style="cursor: pointer;"><?include "list1.php";?></select></td>
			<td><select name="sel2_2" id="sel2_2" style="cursor: pointer;"><?include "list3.php";?></select></td>
			</tr>
			<tr>
			<td colspan="2"><select name="sel3_2" id="sel3_2" style="cursor: pointer;"><?include "list2.php";?></select></td>
			</tr>
		</table>
	</div>
	<div class="var">
		<input type="radio" name="vybor" id="vybor3" value="3" style="clear: left; float: left; margin: 37px 10px; cursor: pointer;"/>
		<table id="tbl_var1" border="1" cellpadding="10px" style="margin-bottom: 10px;">
			<tr>
			<td colspan="2"><select name="sel1_3" id="sel1_3" style="cursor: pointer;"><?include "list2.php";?></select></td>
			</tr>
			<tr>
			<td><select name="sel2_3" id="sel2_3" style="cursor: pointer;"><?include "list1.php";?></select></td>
			<td><select name="sel3_3" id="sel3_3" style="cursor: pointer;"><?include "list3.php";?></select></td>
			</tr>
		</table>
	</div>
	<div class="var">
		<input type="radio" name="vybor" id="vybor4" value="4" style="clear: left; float: left; margin: 37px 10px; cursor: pointer;"/>
		<table id="tbl_var1" border="1" cellpadding="10px" style="margin-bottom: 10px;">
			<tr>
			<td rowspan="2"><select name="sel1_4" id="sel1_4" style="cursor: pointer;"><?include "list2.php";?></select></td><td><select name="sel2_4" id="sel2_4" style="cursor: pointer;"><?include "list1.php";?></select></td>
			</tr>
			<tr>
			<td><select name="sel3_4" id="sel3_4" style="cursor: pointer;"><?include "list3.php";?></select></td>
			</tr>
		</table>
	</div>
	<div class="var">
		<input type="radio" name="vybor" id="vybor5" value="5" checked="check" style="clear: left; float: left; margin: 37px 10px; cursor: pointer;"/>
		<table id="tbl_var1" border="1" cellpadding="10px" style="margin-bottom: 10px;">
			<tr>
			<td><select name="sel1_5" id="sel1_5" style="cursor: pointer;"><?include "list1.php";?></select></td><td rowspan="2"><select name="sel2_5" id="sel2_5" style="cursor: pointer;"><?include "list2.php";?></select></td>
			</tr>
			<tr>
			<td><select name="sel3_5" id="sel3_5" style="cursor: pointer;"><?include "list3.php";?></select></td>
			</tr>
		</table>
	</div>
	<div class="var">
		<input type="radio" name="vybor" id="vybor6" value="6" style="clear: left; float: left; margin: 37px 10px; cursor: pointer;"/>
		<table id="tbl_var1" border="1" cellpadding="10px" style="margin-bottom: 10px;">
			<tr>
			<td><select name="sel1_6" id="sel1_6" style="cursor: pointer;"><?include "list1.php";?></select></td><td><select name="sel2_6" id="sel2_6" style="cursor: pointer;"><?include "list2.php";?></select></td>
			</tr>
			<tr>
			<td><select name="sel3_6" id="sel3_6" style="cursor: pointer;"><?include "list3.php";?></select></td><td><select name="sel4_6" id="sel4_6" style="cursor: pointer;"><?include "list4.php";?></select></td>
			</tr>
		</table>
	</div>
	<input type="submit" value="Продолжить" style="cursor: pointer; margin-left: 180px;"/>
	</form>
</div>
</body>
</html>
<?
}else{
?>
<!DOCTYPE  HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>GPS control 2</title>
</head>
<?
$site1 = "cars.php?m=4";
$site2 = "osm.php";
$site3 = "carspoi.php";
$site4 = "http://bpr_serv";
$site5 = "http://bpr_serv/rel/routes.php?user=tab";

if ($w1 == 'cars'){
	$scroll1 = "";
	$adr1 = $site1;
}else{
	if ($w1 == 'osm') $adr1 = $site2;
	if ($w1 == 'carspoi') $adr1 = $site3;
	if ($w1 == 'gps') $adr1 = $site4;
	if ($w1 == 'routes') $adr1 = $site5;
	$scroll1 = "scrolling='no'";
}
if ($w2 == 'cars'){
	$scroll2 = "";
	$adr2 = $site1;
}else{
	if ($w2 == 'osm') $adr2 = $site2;
	if ($w2 == 'carspoi') $adr2 = $site3;
	if ($w2 == 'gps') $adr2 = $site4;
	if ($w2 == 'routes') $adr2 = $site5;
	$scroll2 = "scrolling='no'";
}
if ($w3 == 'cars'){
	$scroll3 = "";
	$adr3 = $site1;
}else{
	if ($w3 == 'osm') $adr3 = $site2;
	if ($w3 == 'carspoi') $adr3 = $site3;
	if ($w3 == 'gps') $adr3 = $site4;
	if ($w3 == 'routes') $adr3 = $site5;
	$scroll3 = "scrolling='no'";
}
if ($w4 == 'cars'){
	$scroll4 = "";
	$adr4 = $site1;
}else{
	if ($w4 == 'osm') $adr4 = $site2;
	if ($w4 == 'carspoi') $adr4 = $site3;
	if ($w4 == 'gps') $adr4 = $site4;
	if ($w4 == 'routes') $adr4 = $site5;
	$scroll4 = "scrolling='no'";
}
if ($type == 1){
?>
   <frameset cols="50%,50%">
     <frame src="<?echo $adr1;?>" name="<?echo $w1;?>" <?echo $scroll1;?>>
     <frame src="<?echo $adr2;?>" name="<?echo $w2;?>" <?echo $scroll2;?>>
   </frameset>	
<?
}elseif($type == 2){
?>
<frameset rows="50%,50%">
   <frameset cols="50%,50%">
     <frame src="<?echo $adr1;?>" name="<?echo $w1;?>" <?echo $scroll1;?>>
     <frame src="<?echo $adr2;?>" name="<?echo $w2;?>" <?echo $scroll2;?>>
   </frameset>
     <frame src="<?echo $adr3;?>" name="<?echo $w3;?>" <?echo $scroll3;?>>
</frameset>
<?	
}elseif($type == 3){
?>
<frameset rows="50%,50%">
     <frame src="<?echo $adr1;?>" name="<?echo $w1;?>" <?echo $scroll1;?>>
   <frameset cols="50%,50%">
     <frame src="<?echo $adr2;?>" name="<?echo $w2;?>" <?echo $scroll2;?>>
     <frame src="<?echo $adr3;?>" name="<?echo $w3;?>" <?echo $scroll3;?>>
   </frameset>
</frameset>
<?	
}elseif($type == 4){
?>
<frameset cols="50%,50%">
     <frame src="<?echo $adr1;?>" name="<?echo $w1;?>" <?echo $scroll1;?>>
   <frameset rows="50%,50%">
     <frame src="<?echo $adr2;?>" name="<?echo $w2;?>" <?echo $scroll2;?>>
     <frame src="<?echo $adr3;?>" name="<?echo $w3;?>" <?echo $scroll3;?>>
   </frameset>
</frameset>
<?	
}elseif($type == 5){
?>
<frameset cols="50%,50%">
   <frameset rows="50%,50%">
     <frame src="<?echo $adr1;?>" name="<?echo $w1;?>" <?echo $scroll1;?>>
     <frame src="<?echo $adr3;?>" name="<?echo $w3;?>" <?echo $scroll3;?>>
   </frameset>
     <frame src="<?echo $adr2;?>" name="<?echo $w2;?>" <?echo $scroll2;?>>
</frameset>
<?	
}elseif($type == 6){
?>
<frameset cols="50%,50%">
   <frameset rows="50%,50%">
     <frame src="<?echo $adr1;?>" name="<?echo $w1;?>" <?echo $scroll1;?>>
     <frame src="<?echo $adr3;?>" name="<?echo $w3;?>" <?echo $scroll3;?>>
   </frameset>
   <frameset rows="50%,50%">   
     <frame src="<?echo $adr2;?>" name="<?echo $w2;?>" <?echo $scroll2;?>>
     <frame src="<?echo $adr4;?>" name="<?echo $w4;?>" <?echo $scroll4;?>>
   </frameset>
</frameset>
<?	
}
?>
</html>
<?
}
?>