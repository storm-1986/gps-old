<!doctype html>
<html lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<?php

include_once "options.php";

$rConn = ads_connect("DataDirectory=\\\\".$adsdbpath.";ServerTypes=7", "AdsSys", "");

if ( $rConn == False ){
	$strErrCode = ads_error( );
	$strErrString = ads_errormsg( );
	echo "Connection failed: " . $strErrCode . " " . $strErrString . "<br>\n";
	exit;
}

// Для SP_USER
/*
$qexport = ads_do($rConn, "SELECT * FROM SP_USER");
	while (ads_fetch_row($qexport)){
		$uid = ads_result($qexport, "USER_ID");
		$login = ads_result($qexport, "LOGIN");
		$pass = ads_result($qexport, "PASSWORD");
		$descr = ads_result($qexport, "DESCR");
		$role = ads_result($qexport, "ROLE");
		$owner = ads_result($qexport, "OWNER");
		$lctn = ads_result($qexport, "LCTN");
		$kod = ads_result($qexport, "KOD");
		$kod2 = ads_result($qexport, "KOD2");
		$lastdt = ads_result($qexport, "LAST_DATE");
		$counter = ads_result($qexport, "COUNTER");
		$fails = ads_result($qexport, "FAIL");
		$rolename = ads_result($qexport, "ROLENAME");
		$q_zapros = "INSERT INTO SP_USER (LOGIN, PASSWORD, DESCR, ROLE, OWNER, LCTN, KOD, KOD2, LAST_DATE, COUNTER, FAIL, ROLENAME)  VALUES ('".$login."', '".$pass."', '".$descr."',".$role.", ".$owner.", '".$lctn."', '".$kod."', '".$kod2."', '".$lastdt."', ".$counter.", ".$fails.", '".$rolename."')";
		echo $q_zapros."<br>";
		$qimport = sqlsrv_query($conn, $q_zapros);
	}
*/

// Для SP_OWNER
/*
$qdel = sqlsrv_query($conn, "TRUNCATE TABLE SP_OWNER");
$qset = sqlsrv_query($conn, "SET IDENTITY_INSERT SP_OWNER ON");
$qexport = ads_do($rConn, "SELECT * FROM SP_OWNER");
	while (ads_fetch_row($qexport)){
		$owner = ads_result($qexport, "OWNER");
		$oname = trim(ads_result($qexport, "NAME"));
		$q_zapros = "INSERT INTO SP_OWNER (OWNER, NAME) VALUES (".$owner.", '".$oname."')";
		echo $q_zapros."<br>";
		$qimport = sqlsrv_query($conn, $q_zapros);
	}
$qset = sqlsrv_query($conn, "SET IDENTITY_INSERT SP_OWNER OFF");
*/

// Для SP_LCTN
/*
$qexport = ads_do($rConn, "SELECT * FROM SP_LCTN");
	while (ads_fetch_row($qexport)){
		$lctn = ads_result($qexport, "LCTN");
		$dscr = trim(ads_result($qexport, "DSCR"));
		$base = trim(ads_result($qexport, "BASE"));
		$port = trim(ads_result($qexport, "PORT"));
		$map = trim(ads_result($qexport, "MAP"));
		$q_zapros = "INSERT INTO SP_LCTN VALUES (".$lctn.",'".$dscr."','".$base."',".$port.",'".$map."')";
		echo $q_zapros."<br>";
		$qimport = sqlsrv_query($conn, $q_zapros);
	}
*/
// Для SP_CARS
/*
$i == 0;
$qdel = sqlsrv_query($conn, "TRUNCATE TABLE SP_CARS");
$qexport = ads_do($rConn, "SELECT * FROM SP_CARS");
	while (ads_fetch_row($qexport)){
		$i++;
		$anum = trim(ads_result($qexport, "ANUM"));
		$lctn = ads_result($qexport, "LCTN");
		$auto = trim(ads_result($qexport, "AUTO"));
		$owner = ads_result($qexport, "OWNER");
		$driver = trim(ads_result($qexport, "DRIVER"));
		$tel = trim(ads_result($qexport, "TELDRV"));
		$holod = trim(ads_result($qexport, "HOLOD"));
		$pf = ads_result($qexport, "PF");
		$q_zapros = "INSERT INTO SP_CARS VALUES ('".$anum."',".$lctn.",'".$auto."',".$owner.",'".$driver."','".$tel."','".$holod."',".$pf.")";
		echo $q_zapros."<br>";
		$qimport = sqlsrv_query($conn, $q_zapros);
	}
echo "Импортировано ".$i." записей.";
*/
// Для BD_TREKLOG
/*
$qexport = ads_do($rConn, "SELECT * FROM BD_TREKLOG");
	$qset = sqlsrv_query($conn, "SET IDENTITY_INSERT BD_TREKLOG ON");
	while (ads_fetch_row($qexport)){
		$id = ads_result($qexport, "ID");
		$anum = trim(ads_result($qexport, "ANUM"));
		$sern = ads_result($qexport, "SERN");
		$owner = ads_result($qexport, "OWNER");
		$notes = trim(ads_result($qexport, "NOTES"));
		$notes = iconv("utf-8","windows-1251",$notes);
		$login = trim(ads_result($qexport, "LOGIN"));
		$dt = ads_result($qexport, "DT");
		$q_zapros = "INSERT INTO BD_TREKLOG (ID, ANUM, SERN, OWNER, NOTES, LOGIN, DT) VALUES (".$id.",'".$anum."',".$sern.",".$owner.",'".$notes."','".$login."','".$dt."')";
		echo $q_zapros."<br>";
		$qimport = sqlsrv_query($conn, $q_zapros);
		if( $qimport === false ){
   			die( FormatErrors( sqlsrv_errors(), true));
		}
	}
	$qset = sqlsrv_query($conn, "SET IDENTITY_INSERT BD_TREKLOG OFF");
*/

// Для GPS_DEV
	$i == 0;
	$qdel = sqlsrv_query($conn, "TRUNCATE TABLE GPS_DEV");
	$qset = sqlsrv_query($conn, "SET IDENTITY_INSERT GPS_DEV ON");
	$qexport = ads_do($rConn, "SELECT * FROM GPS_DEV");
	while (ads_fetch_row($qexport)){
		$i++;
		$sern = ads_result($qexport, "SERN");
		$dev = ads_result($qexport, "DEV");
		if ($dev == '') $dev = 'NULL';
		$anum = trim(ads_result($qexport, "ANUM"));
		$tmpr = ads_result($qexport, "TMPR");
		if ($tmpr == '') $tmpr = 'NULL';
		$fuel = ads_result($qexport, "FUEL");
		if ($fuel == '') $fuel = 'NULL';
		$phn = ads_result($qexport, "PHN");
		if ($phn == '') $phn = 'NULL';
		$teldev = trim(ads_result($qexport, "TELDEV"));
		$case = ads_result($qexport, "CASE");
		if ($case == '') $case = 'NULL';
		$mntr = ads_result($qexport, "MNTR");
		if ($mntr == '') $mntr = 'NULL';
		$imei = ads_result($qexport, "IMEI");
		$dt = ads_result($qexport, "DEVDT");
		if ($dt == ''){
			$dt = 'NULL';
			$q_zapros = "INSERT INTO GPS_DEV (SERN,DEV,ANUM,TMPR,FUEL,PHN,TELDEV,[CASE],MNTR,IMEI,DEVDT) VALUES (".$sern.",".$dev.",'".$anum."',".$tmpr.",".$fuel.",".$phn.",'".$teldev."',".$case.",".$mntr.",'".$imei."',".$dt.")";
		}else{
			$q_zapros = "INSERT INTO GPS_DEV (SERN,DEV,ANUM,TMPR,FUEL,PHN,TELDEV,[CASE],MNTR,IMEI,DEVDT) VALUES (".$sern.",".$dev.",'".$anum."',".$tmpr.",".$fuel.",".$phn.",'".$teldev."',".$case.",".$mntr.",'".$imei."','".$dt."')";
		}
		echo $q_zapros."<br>";
		$qimport = sqlsrv_query($conn, $q_zapros);
		if( $qimport === false ){
   			die( FormatErrors( sqlsrv_errors(), true));
		}
	}
	$qset = sqlsrv_query($conn, "SET IDENTITY_INSERT GPS_DEV OFF");
	echo "Импортировано ".$i." записей.";
// Для GPS_LAST
/*
	$qdel = sqlsrv_query($conn, "TRUNCATE TABLE GPS_LAST");
	$qexport = ads_do($rConn, "SELECT * FROM GPS_LAST");
	while (ads_fetch_row($qexport)){
		$sern = ads_result($qexport, "SERN");
		if ($sern == '') $sern = 'NULL'; 
		$stat = ads_result($qexport, "STATUS");
		if ($stat == '') $stat = 'NULL'; 
		$total = ads_result($qexport, "TOTAL");
		$err = ads_result($qexport, "ERR");
		$ldate = trim(ads_result($qexport, "LASTDATE"));
		$insdate = substr($ldate, 0,4)."-".substr($ldate, 4,2)."-".substr($ldate, 6,2);
		$ltime = trim(ads_result($qexport, "LASTTIME"));
		$lat = ads_result($qexport, "LASTLAT");
		$lon = ads_result($qexport, "LASTLONG");
		$speed = ads_result($qexport, "LASTSPEED");
		$tmpr = ads_result($qexport, "LASTTMPR");
		$fuel = ads_result($qexport, "LASTFUEL");
		if ($fuel < 0.01) $fuel = 0;
		$anum = trim(ads_result($qexport, "ANUM"));
		$q_zapros = "INSERT INTO GPS_LAST VALUES (".$sern.",".$stat.",".$total.",".$err.",'".$insdate."','".$ltime."',".$lat.",".$lon.",".$speed.",".$tmpr.",".$fuel.",'".$anum."')";
		echo $q_zapros."<br>";
		$qimport = sqlsrv_query($conn, $q_zapros);
		if( $qimport === false ){
   			die( FormatErrors( sqlsrv_errors(), true));
		}
	}
*/
?>
</body>
</html>