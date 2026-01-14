<!doctype html>
<html lang="ru">
  <head>
    <link rel="stylesheet" type="text/css" href="css/osm_design.css?n=<?echo mt_rand();?>"/>
	<link rel="stylesheet" type="text/css" href="css/leaflet.css"/>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script src="js/leaflet.js" type="text/javascript"></script>
	<script src="js/leaflet.polylineDecorator.js" type="text/javascript"></script>
	<script src="js/osm.js?k=777" type="text/javascript"></script>

  </head>
  <body onload="init()">
	<div id="info"></div>
	<div id="map"></div>
  </body>
</html>