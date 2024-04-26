<link rel="stylesheet" href="css/leaflet.css"/>
<link rel="stylesheet" href="css/L.Control.ZoomDisplay.css"/>
<link rel="stylesheet" href="css/Control.Coordinates.css"/>
<link rel="stylesheet" href="css/font-awesome.css"/>
<link rel="stylesheet" href="css/leaflet.awesome-markers.css"/>
<link rel="stylesheet" href="css/leaflet.draw.css" />
<link rel="stylesheet" href="css/leaflet.measurecontrol.css" />
<!--
<script type="text/javascript" src="js/draganddrop.js"></script>
-->
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/jquery.canvasjs.min.js"></script>
<script type="text/javascript" src="js/leaflet.js"></script>
<script type="text/javascript" src="js/leaflet.polylineDecorator.js"></script>
<script type="text/javascript" src="js/L.Control.ZoomDisplay.js"></script>
<script type="text/javascript" src="js/Control.Coordinates.js"></script>
<script type="text/javascript" src="js/leaflet.awesome-markers.js"></script>
<script type="text/javascript" src="js/leaflet.draw.js"></script>
<script type="text/javascript" src="js/leaflet.measurecontrol.js"></script>
<script type="text/javascript" src="js/L.Polyline.measuredDistance.js"></script>

<!-- Календарь -->
<script type="text/javascript" src="js/jquery.datetimepicker.full.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>

<!-- Выпадающий список с поиском -->
<script type="text/javascript" src="js/jquery.immybox.min.js"></script>

<!-- bootstrap -->
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="css/immybox.css"/>

<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/tracks.js?t=112233"></script>
<script type="text/javascript" src="js/online.js"></script>

<script type="text/javascript">
	var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
        // IE 10 or older => return version number
 		alert("Вы используете браузер Internet Explorer. Для нормальной работы сайта следует использовать браузер Chrome.");
    }
</script>
</head>
<body onload="<?php if (isset($_GET['reponmap'])){ echo "init(); rmarker();";} elseif (isset($_GET['osm']) || @$_GET['ponline'] == 3){ echo "init()";}?>">
<nav id ="menu" class="navbar navbar-expand-lg navbar-dark bg-dark">
<span class="navbar-brand"><?= $_SESSION['username'];?></span>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
   	<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNavDropdown">
<ul class="navbar-nav">
	<li class="nav-item">
		<a class="nav-link <?php if (isset($_GET['opis'])) echo "active"?> px-md-3" href="?opis=1">Описание</a>
	</li>
    <li class="nav-item">
    	<a class="nav-link <?php if (isset($_GET['osm'])) echo "active"?> px-md-3" href="?osm=1">Карты OpenStreet</a>
	</li>
<?php
if ($_SESSION['username'] !== 'velcom'){
?>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle <?php if (isset($_GET['ponline'])) echo "active"?> px-md-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Сервисы</a>
		<div class="dropdown-menu">
			<a class="dropdown-item" href="?ponline=1">Машины с данными за сегодня</a>
			<a class="dropdown-item" href="?ponline=2">Нерабочие навигации</a>
			<!-- <a class="dropdown-item" href="?ponline=3">Перемещение online</a> -->
		</div>
	</li>
<?php
if ($_SESSION['usertype'] >= 1){
?>
	<!-- <li class="nav-item">
		<a class="nav-link <?php if (isset($_GET['treker'])) echo "active"?> px-md-3" href="?treker=1">Трекеры</a>
	</li> -->
<?php
}
if ($_SESSION['usertype'] == 1){
?>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle <?php if (isset($_GET['admin'])) echo "active"?> px-md-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Админка</a>
		<div class="dropdown-menu">
			<a class="dropdown-item" href="?admin=3">Справочник автомобилей</a>
			<a class="dropdown-item" href="?admin=1">Справочник перевозчиков</a>
			<a class="dropdown-item" href="?admin=2">Управление пользователями</a>
		</div>
	</li>
<?php
}
}
?>
	<li class="nav-item">
		<a class="nav-link px-md-3" href="?exit=1">Выход</a>
	</li>
</ul>
</div>
</nav>
<input name="uname" type="hidden" id="u_id" value="<?= $_SESSION['username']?>"/>
<input name="uowner" type="hidden" id="u_own" value="<?= $_SESSION['userowner']?>"/>
<input name="utype" type="hidden" id="u_tp" value="<?= $_SESSION['usertype']?>"/>