<?php
session_start();

if (empty($_SESSION['id']) || $_SESSION['id'] !== session_id()) {
    header('Location: /index.php');
    exit;
}
?>
<!DOCTYPE  HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>GPS control 2</title>
</head>
<?php
$site1 = "cars.php?m=4";
$site2 = "osm.php";
$adr1 = $site1;
$w1 = 'cars';
$scroll1 = "";
$adr2 = $site2;
$w2 = 'osm';
$scroll2 = "scrolling='no'";
?>
<frameset cols="50%,50%">
	<frame src="<?php echo $adr1;?>" name="<?php echo $w1;?>" <?php echo $scroll1;?>>
	<frame src="<?php echo $adr2;?>" name="<?php echo $w2;?>" <?php echo $scroll2;?>>
</frameset>	
</html>