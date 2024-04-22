<div id="dr2">
<div id="orders"></div>
<div id="docs"></div>
<div class="zot">Маршруты</div>
<form id="f1" name="mform" method="post">
<div class="tdstyle">
<?
	include 'date2.htm';
?>
<input type="submit" name="seldt" value="Применить" class="linp"/>
</div>
</form>
<?
$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ( $rConn == False ){
	$strErrCode = ads_error( );
	$strErrString = ads_errormsg( );
	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\n";
}
else{
if (isset($_POST['seldt'])){
	$beg_d = $_POST['sch'];
	$beg_m = $_POST['smes'];
	$beg_y = $_POST['sgod'];
	$end_d = $_POST['poch'];
	$end_m = $_POST['pomes'];
	$end_y = $_POST['pogod'];
	if ($_SESSION['userrole'] == 0) $rquery = "SELECT * FROM BD_ROUTE WHERE DT >= '".$beg_y.$beg_m.$beg_d."' AND DT <= '".$end_y.$end_m.$end_d."' ORDER BY ANUM";
	if ($_SESSION['userrole'] == 1) $rquery = "SELECT * FROM BD_ROUTE WHERE ZT1_ID = '".$_SESSION['userkod']."' AND DT >= '".$beg_y.$beg_m.$beg_d."' AND DT <= '".$end_y.$end_m.$end_d."' ORDER BY ANUM";
	if ($_SESSION['userrole'] == 2) $rquery = "SELECT * FROM BD_ROUTE WHERE ATP1_ID = '".$_SESSION['userkod']."' AND DT >= '".$beg_y.$beg_m.$beg_d."' AND DT <= '".$end_y.$end_m.$end_d."' ORDER BY ANUM";
	if ($_SESSION['userrole'] == 3) $rquery = "SELECT DISTINCT ROUTE_ID FROM BD_ORDER WHERE PP1_ID = '".$_SESSION['userkod']."' AND DTPP >= '".$beg_y.$beg_m.$beg_d."' AND DTPP <= '".$end_y.$end_m.$end_d."'";
	if ($_SESSION['userrole'] == 4) $rquery = "SELECT DISTINCT ROUTE_ID FROM BD_ORDER WHERE PD1_ID = '".$_SESSION['userkod']."' AND DTPP >= '".$beg_y.$beg_m.$beg_d."' AND DTPP <= '".$end_y.$end_m.$end_d."'";
}else{
	$today = date(Ymd);
	if ($_SESSION['userrole'] == 0)	$rquery = "SELECT * FROM BD_ROUTE WHERE DT = '".$today."' ORDER BY ANUM";													// для админа
	if ($_SESSION['userrole'] == 1)	$rquery = "SELECT * FROM BD_ROUTE WHERE ZT1_ID = '".$_SESSION['userkod']."' AND DT = '".$today."' ORDER BY ANUM";			// для заказчика
	if ($_SESSION['userrole'] == 2)	$rquery = "SELECT * FROM BD_ROUTE WHERE ATP1_ID = '".$_SESSION['userkod']."' AND DT = '".$today."'   ORDER BY ANUM";		// для перевозчика
	if ($_SESSION['userrole'] == 3) $rquery = "SELECT DISTINCT ROUTE_ID FROM BD_ORDER WHERE PP1_ID = '".$_SESSION['userkod']."' AND DTPP = '".$today."'";		// для грузоотправителя
	if ($_SESSION['userrole'] == 4) $rquery = "SELECT DISTINCT ROUTE_ID FROM BD_ORDER WHERE PD1_ID = '".$_SESSION['userkod']."' AND DTPP = '".$today."'";		// для грузоотправителя
}
	$res_rquery = ads_do($rConn, $rquery);
	$i = 0;
	while (ads_fetch_row($res_rquery)){
		$i++;
		if ($_SESSION['userrole'] == 3 || $_SESSION['userrole'] == 4){
			$r_rid = ads_result($res_rquery, "ROUTE_ID");
			$rquery2 = "SELECT * FROM BD_ROUTE WHERE ROUTE_ID = ".$r_rid." ORDER BY ANUM"; // для грузоотправителя
			$res_rquery2 = ads_do($rConn, $rquery2);

		$r_rid = ads_result($res_rquery2, "ROUTE_ID");
		$r_anum = ads_result($res_rquery2, "ANUM");
		$r_per = ads_result($res_rquery2, "ATP1_ID");
		$r_date = ads_result($res_rquery2, "DT");
		$r_npl = ads_result($res_rquery2, "NUMPL");
		$r_mest = ads_result($res_rquery2, "KOLM");
		$r_ves = ads_result($res_rquery2, "VB");
		$r_len = ads_result($res_rquery2, "RTLEN");
		$r_stat = ads_result($res_rquery2, "RSTAT");
		}else{
		$r_rid = ads_result($res_rquery, "ROUTE_ID");
		$r_anum = ads_result($res_rquery, "ANUM");
		$r_per = ads_result($res_rquery, "ATP1_ID");
		$r_date = ads_result($res_rquery, "DT");
		$r_npl = ads_result($res_rquery, "NUMPL");
		$r_mest = ads_result($res_rquery, "KOLM");
		$r_ves = ads_result($res_rquery, "VB");
		$r_len = ads_result($res_rquery, "RTLEN");
		$r_stat = ads_result($res_rquery, "RSTAT");
		}		
		$q_colzak = "SELECT COUNT (ROUTE_ID) AS \"ord\" FROM BD_ORDER WHERE ROUTE_ID = ".$r_rid;
		$res_colzak = ads_do($rConn, $q_colzak);
		$col_zak = ads_result($res_colzak,'ord');
		$q_coldoc = "SELECT COUNT (ROUTE_ID) AS \"ord\" FROM BD_DOC WHERE ROUTE_ID = ".$r_rid;
		$res_coldoc = ads_do($rConn, $q_coldoc);
		$col_doc = ads_result($res_coldoc,'ord');
		$new_tr .= "<tr class=\"tptr2\"><td class=\"tdol1\">".$r_anum."</td><td class=\"tdol1\">".$r_per."</td><td class=\"tdol1\">".substr($r_date,6).".".substr($r_date,4,2).".".substr($r_date,0,4)."</td><td class=\"tdol1\">".$r_npl."</td><td class=\"tdol1\">".$r_mest."</td><td class=\"tdol1\">".$r_ves."</td><td class=\"tdol1\">".$r_len."</td><td class=\"tdol1\">".$r_stat."</td><td class=\"tdol1\"><div class=\"imit_a\" onclick=\"f_orders(".$r_rid.")\">Заказы (".$col_zak.")</div></td><td class=\"tdol1\"><div class=\"imit_a\" onclick=\"f_docs(".$r_rid.")\">Документы (".$col_doc.")</div></td><td class=\"tdol1\">Показать</td></tr>";
	}
	if ($i > 0){
?>
<div class="notification"><?echo "C $beg_d.$beg_m.$beg_y по $end_d.$end_m.$end_y";?></div>
<table id="tponline" cellpadding="0" cellspacing="0">
	<tr class="tptr"><td class="tdol1">Машина</td><td class="tdol1">Перевозчик</td><td class="tdol1">Дата отгрузки</td><td class="tdol1">№ путевого</td><td class="tdol1">Мест</td><td class="tdol1">Вес</td><td class="tdol1">Длина маршрута (план)</td><td class="tdol1">Статус</td><td class="tdol1">Заказы</td><td class="tdol1">Документы</td><td class="tdol1">На карте</td></tr>
<?
	echo $new_tr;
?>
</table>
<?
	}else{
?>
	<div class="notification">Нет маршрутов для выбранной даты</div>
<?
	}
if(isset($_FILES['upldoc'])){
	$rid = intval($_POST['rid']);
	if ($_POST['oid'] > 0){
		$oid = intval($_POST['oid']);
	}else{
		$oid = 'null';
	}
    //Список разрешенных файлов
    $whitelist = array(".doc", ".docx", ".xls", ".xlsx", ".pdf");
    $data = array();
    $error = true;                
    //Проверяем разрешение файла
    foreach  ($whitelist as  $item){
        if(preg_match("/$item\$/i",$_FILES['upldoc']['name'])) $error = false;
    }

    //если нет ошибок, грузим файл
    if(!$error){
                 
        $folder =  'test/';//директория в которую будет загружен файл
        
        $uploadedFile =  $folder.basename($_FILES['upldoc']['name']);
                
        if(is_uploaded_file($_FILES['upldoc']['tmp_name'])){

			$fname = addslashes($_FILES['upldoc']['name']);
			$f_ext = substr($fname,-4);
			$f_ext = trim($f_ext, ".");
			$f_ext = strtolower ($f_ext);			
			$fname = iconv("UTF-8", "windows-1251", $fname);

			$f=fopen($_FILES['upldoc']['tmp_name'],"rb");
			$fupload=fread($f,filesize($_FILES['upldoc']['tmp_name'])); // считали файл в переменную
			fclose($f); // закрыли файл, можно опустить

//			$fupload = file_get_contents($_FILES['upldoc']['tmp_name']);

			$fupload = base64_encode($fupload);
			$fupload=addslashes($fupload);
        	
			$id_max= "SELECT MAX (DOC_ID) AS \"idmax\" FROM BD_DOC";
			$res_id_max = ads_do($rConn, $id_max);
			$idmax = ads_result($res_id_max,'idmax');
			$new_iddoc = $idmax + 1;

        	$insdoc = "INSERT INTO BD_DOC VALUES (".$new_iddoc.", ".$oid.", ".$rid.", 0, '".$f_ext."', '".$fupload."', '".$_SESSION['username']."', '".$fname."', now())";
        	$res_insdoc = ads_do($rConn, $insdoc);


            if($res_insdoc){
                $data = $_FILES['upldoc'];
            }
            else {   
                $data['errors'] = "Во время загрузки файла произошла ошибка";
            }

        }
        else {    
            $data['errors'] = "Файл не  загружен";
        }
    }
    else{
        $data['errors'] = 'Вы загружаете запрещенный тип файла. Загружать можно документы word, excel, pdf';
    }
    //Формируем js-файл    
    $res = '<script type="text/javascript">';
    $res .= "var data = new Object;";
    foreach($data as $key => $value){
        $res .= 'data.'.$key.' = "'.$value.'";';
    }
    $res .= 'window.parent.handleResponse(data);';
    $res .= 'window.parent.f_docs('.$rid.','.$oid.');';
    $res .= "</script>";
    
    echo $res;
}
}
?>
</table>
</div>