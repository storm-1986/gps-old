function init(save_w,save_h){

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

var url = window.location.href;
var zavod = 1;
if (url.substring(7,15) !== 'bpr_serv') zavod = 0;

	
if (navigator.userAgent.match("MSIE")){
	vs = document.documentElement.clientHeight;
	var sh = document.documentElement.clientWidth
}
else{
	vs = window.innerHeight;
	var sh = window.innerWidth
}
if (sh < 800) sh = 800;
if (vs < 600) vs = 600;

jQuery('#map').css('height',vs+'px');

if (save_w > 0){
	var max_lat = jQuery('#s_maxlat').val(),
	min_lat = jQuery('#s_minlat').val(),
	max_lon = jQuery('#s_maxlon').val(),
	min_lon = jQuery('#s_minlon').val();
/*
	if ((max_lat > 0) && (min_lat > 0) && (max_lon > 0) && (min_lon > 0)){
		var dot1 = L.latLng(max_lat, min_lon),
		dot2 = L.latLng(min_lat, min_lon),
		dot3 = L.latLng(min_lat, max_lon),
		dist_v = dot1.distanceTo(dot2),
		dist_g = dot2.distanceTo(dot3),
		vs = dist_v*save_w/dist_g;
	}
*/
	jQuery('#map').css('width',save_w+'px');
	jQuery('#map').css('height',save_h+'px');
	
}


//arr = [];	/*	Массив для записи номеров маршрутов (используется в grid_script.js)*/

/*
var localUrl = '../../map/images/osm_tiles/{z}/{x}/{y}.png';
*/
/*
var labelTextCollision = new L.LabelTextCollision({
  collisionFlg : true
});
*/
var localUrl = 'http://cis-tile.savushkin.by/osm_tiles/{z}/{x}/{y}.png';
var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';

var inet_osm  = L.tileLayer(osmUrl, {attribution: osmAttrib, maxZoom: 18});
var local_osm = L.tileLayer(localUrl, {attribution: osmAttrib, maxZoom: 18});

if(isMobile.any() || zavod == 0){
	map = L.map('map', {
			center: [52.623060, 25.515747],
			zoom: 8,
			doubleClickZoom: false,
/*			renderer : labelTextCollision,*/			
			layers: [inet_osm]
	});
	var baseLayers = {
			"Карта OSM": inet_osm
	}
}else{
	map = L.map('map', {
			center: [52.623060, 25.515747],
			zoom: 8,
			doubleClickZoom: false,
/*			renderer : labelTextCollision,*/			
			layers: [local_osm]
	});
	var baseLayers = {
			"Локальная карта OSM": local_osm,
			"Карта OSM": inet_osm
	};
}

L.control.scale().addTo(map);
L.Control.measureControl().addTo(map);

layerControl = L.control.layers(baseLayers).addTo(map);

if (save_w < 0){			/*	убираем ненужные контролы при сохранении скриншота	*/

var customControl = L.Control.extend({

  options: {
    position: 'topleft' 
  },

  onAdd: function (map) {
    var container = L.DomUtil.create('div', 'leaflet-findbtn');

    container.onclick = function(){
      shform();
    }

    return container;
  }
});

map.addControl(new customControl());


var c = new L.Control.Coordinates().addTo(map);

map.on('mousemove', function(e) {
    c.setCoordinates(e);
});

_getClosestPointIndex = function(lPoint, arrayLPoints) {
	var distanceArray = [];
	for ( var i = 0; i < arrayLPoints.length; i++ ) {
		distanceArray.push( lPoint.distanceTo(arrayLPoints[i]) );
	}
	return distanceArray.indexOf(  Math.min.apply(null, distanceArray) );
}
}

map.on('dblclick', function(e) {
	text = e.latlng.lat.toFixed(6) + ", " + e.latlng.lng.toFixed(6);
	window.prompt ("Чтобы скопировать текст в буфер обмена, нажмите Ctrl+C", text);
})

map.on('click', function(e) {
	if ($('#sub_0').is(':visible')){
		$('#sub_0').css('display','none');
	}
})

LeafIcon = L.Icon.extend({
	options: {
    	shadowUrl: 'images/marker-shadow.png',
        iconSize:     [25, 41],
        shadowSize:   [41, 41],
        iconAnchor:   [12, 41],
        shadowAnchor: [12, 41],
        popupAnchor:  [0, -41]
    }
});
green = new LeafIcon({iconUrl: 'images/marker-green.png'});
red = new LeafIcon({iconUrl: 'images/marker-red.png'});
yellow = new LeafIcon({iconUrl: 'images/marker-yellow.png'});


if (save_w >= 0){									/*		!!!!!!!!!!!!!!!!!!!!		СОХРАНЕНИЕ КАРТИНКИ		!!!!!!!!!!!!!		*/
	setTimeout(function(){
	$('#map').html2canvas({
		flashcanvas: "js/flashcanvas.min.js",
		proxy: 'proxy.php',
		logging: false,
		profile: false,
		useCORS: false
	});
	}, 1500);	/*задержка для скриншота*/
}
}
/*
function showstops(car){
	StopMarkers = new OpenLayers.Layer.Text("Маркеры стоянок "+car, {location:"markers/stops/"+car+".txt?n="+Math.random(), projection: new OpenLayers.Projection("EPSG:4326")});
	map.addLayer(StopMarkers);
}
*/
function gototr(trcar){
	map.fitBounds(trcar.getBounds());
}

function gotosub(lat,lon){
	map.setView(new L.LatLng(lat, lon), 17);
}

function ferms(){
	var user = jQuery('#user').val();
	var url = 'get_ferms.php';
	jQuery.get(
		url,
		"user=" + user,
		function (result){
			if (result.type == 'error'){
				alert (result.errmsg);
				return(false);
			}
			else{
				if (window.marker){
					for(i=0;i<marker.length;i++){
						map.removeLayer(marker[i]);
					}
				}
				$('#route_info').html('');
				var inshidel = '';
				marker = new Array();
				var i = 0;
				jQuery(result.ar_ferms).each(function(){
					var params = jQuery(this).attr('params');
					var forpopup = "";
					var counter = 0;
					jQuery(params).each(function(){
						if (counter == 0){
							var ch_fl = 'checked';
						}else{
							var ch_fl = '';
						}
						var r_nm = jQuery(this).attr('r_id').split('_')[1];
						forpopup += "<input type = 'radio' name = 'reis' id = 'reis"+jQuery(this).attr('nz')+"' value = '"+jQuery(this).attr('nz')+"' "+ch_fl+"> <label for='reis"+jQuery(this).attr('nz')+"' onClick=select_mrow('"+jQuery(this).attr('r_id')+"')><b>"+jQuery(this).attr('anum')+"</b> "+r_nm+" №"+jQuery(this).attr('npp')+", вес: <b>"+jQuery(this).attr('ves')+"</b> тонн,<br/> пл. время: <b>"+jQuery(this).attr('dt_reisa')+"</b>, р. время: <b>"+jQuery(this).attr('dt_fact')+"</b>, смена: "+jQuery(this).attr('smena')+"</label><br/>";
						inshidel += "<input type='hidden' id='reis_id"+jQuery(this).attr('nz')+"' value='"+jQuery(this).attr('r_id')+"'><input type='hidden' id='npp"+jQuery(this).attr('nz')+"' value='"+jQuery(this).attr('npp')+"'>";
						counter++;
					});
					$('#route_info').html(inshidel);
					if (~jQuery(this).attr('ferm_id').indexOf("_")){
						var textpopup = "<table><tr><td colspan='2'><b>"+jQuery(this).attr('ferm_name')+"</b></td></tr><tr><td colspan='2'><b>"+jQuery(this).attr('hoz')+"</b></td></tr><tr><td><b>подъезд:</b></td><td>"+jQuery(this).attr('ferm_pdzd')+"т.</td></tr><tr><td width='100px'><b>Тип стоянки:</b></td><td>"+jQuery(this).attr('ferm_tstop')+"</td></tr><tr><td><b>Лаб. контроль: </b></td><td>"+jQuery(this).attr('ferm_lab')+"</td></tr><tr><td><b>Тип авто: </b></td><td>"+jQuery(this).attr('tip_auto')+"</td></tr></table><br/>Точка используется в следующих маршрутах:<br/>"+forpopup+"<input type='button' class='popupbtn' value='Прикрепить к другому маршруту' onclick=dialwin('"+jQuery(this).attr('ferm_id')+"',"+jQuery(this).attr('ferm_lat')+","+jQuery(this).attr('ferm_lon')+")><br/><input type='button' class='popupbtn' value='Изменить позицию в маршруте' onclick=dialwin2('"+jQuery(this).attr('ferm_id')+"',"+jQuery(this).attr('ferm_lat')+","+jQuery(this).attr('ferm_lon')+")>"
					}else{
						var textpopup = "<table><tr><td colspan='2'><b>"+jQuery(this).attr('ferm_name')+"</b></td></tr><tr><td colspan='2'><b>"+jQuery(this).attr('hoz')+"</b></td></tr><tr><td><b>подъезд:</b></td><td>"+jQuery(this).attr('ferm_pdzd')+"т.</td></tr><tr><td width='100px'><b>Тип стоянки:</b></td><td>"+jQuery(this).attr('ferm_tstop')+"</td></tr><tr><td><b>Лаб. контроль: </b></td><td>"+jQuery(this).attr('ferm_lab')+"</td></tr><tr><td><b>Тип авто: </b></td><td>"+jQuery(this).attr('tip_auto')+"</td></tr></table>";
					}
					var LamMarker = new L.marker([jQuery(this).attr('ferm_lat'), jQuery(this).attr('ferm_lon')],{title: jQuery(this).attr('ferm_id')}).bindPopup(textpopup);
					marker.push(LamMarker);
					map.addLayer(marker[i]);
					i++;
				});
			}
		},
		"json"
	);
}

function select_mrow(id_mrow){
	jQuery('#list').jqGrid('setSelection',id_mrow);
}

function shform(){
	$("#searchform").fadeToggle("slow");
	$("#saddr").prop("value","");
	$("#sstreet").prop("value","");
	$("#shome").prop("value","");
	$("#scont").prop("value","");
	$("#skoord").prop("value","");
}

function shdiv(el){
	$("#"+el).slideToggle("slow");
	$("#s"+el).focus();
	if (el == 'koord'){
		if ($("#addr").is(':visible')) $("#addr").slideToggle("slow");
		if ($("#cont").is(':visible')) $("#cont").slideToggle("slow");
	}
	if (el == 'addr'){
		if ($("#koord").is(':visible')) $("#koord").slideToggle("slow");
		if ($("#cont").is(':visible')) $("#cont").slideToggle("slow");
	}
	if (el == 'cont'){
		if ($("#koord").is(':visible')) $("#koord").slideToggle("slow");
		if ($("#addr").is(':visible')) $("#addr").slideToggle("slow");
	}
}

function validate2(event) {
        // Разрешаем: backspace, delete, tab и escape, точку, запятую и пробел
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 188 || event.keyCode == 190 || event.keyCode == 32 || event.keyCode == 110 ||
             // Разрешаем: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+Z
            (event.keyCode == 65 && event.ctrlKey === true) ||
			(event.keyCode == 67 && event.ctrlKey === true) ||
			(event.keyCode == 86 && event.ctrlKey === true) ||
			(event.keyCode == 90 && event.ctrlKey === true) ||    
             // Разрешаем: home, end, влево, вправо
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // Ничего не делаем
                 return;
        }
        else {
            // Убеждаемся, что это цифра, и останавливаем событие keypress
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
        if (event.keyCode == 13) setmkrkoord();
}

function validate3(event) {
        if (event.keyCode == 13) s_context();
}

function setmkrkoord(){
	var latlon = $("#skoord").val();
	if (/^\d{2}\.\d+[,][\s]*\d{2}\.\d+$/.test(latlon)){
		var e_latlon = latlon.split(",");
		L.marker([e_latlon[0],e_latlon[1]]).addTo(map);
		map.setView(new L.LatLng(e_latlon[0],e_latlon[1]), 15);
	}else{
		alert("Проверьте введенные координаты, формат данных: хх.xxxxxx, xx.xxxxxx");
		$("#skoord").focus();
	}
}

function hideloader(){
	if ($("#loader").is(':visible')){
		alert ("Запрос выполняется слишком долго. Сервер перегружен.");
		$("#loader").css("display","none");
	}
}

function s_addr(){
if ($("#saddr").val() == ''){
	alert ("Заполните поле 'Город'");
	$("#saddr").focus();
	return false;
}
if ($("#sstreet").val() == '' && $("#shome").val() !== ''){
	alert ("Заполните поле 'Улица'");
	$("#sstreet").focus();
	return false;	
}
	var sity = jQuery('#saddr').val();
	sity = sity[0].toUpperCase()+sity.substring(1);
	var street = jQuery('#sstreet').val();
	var home = jQuery('#shome').val();
	var url = 'get_search.php';
	$("#loader").css("display","block");
	setTimeout(hideloader, 30000);
	if (window.smarker){
		for(i=0;i<smarker.length;i++){
			map.removeLayer(smarker[i]);
		}
	}
	if (window.s_poly){
		for(i=0;i<s_poly.length;i++){
			map.removeLayer(s_poly[i]);
		}		
	}
	jQuery.post(
		url,
		"sity=" + sity + "&street=" + street + "&home=" + home, 
		function (result){
			$("#loader").css("display","none");
			if (result.type == 'error'){
				alert(result.msg);
			}
			else{
				if (result.s_var == 1){
					smarker = new Array();
					var i = 0;
					jQuery(result.ar_points).each(function(){
						//var NewMarker = new L.marker([jQuery(this).attr('lat'), jQuery(this).attr('lon')],{title: sity}).bindPopup("<b>"+sity+"</b><br/>"+jQuery(this).attr('reg')+", "+jQuery(this).attr('dist'));
						var NewMarker = new L.marker([jQuery(this).attr('lat'), jQuery(this).attr('lon')],{title: sity}).bindPopup("<b>"+sity+"</b>");
						lats = jQuery(this).attr('lat');
						lons = jQuery(this).attr('lon');
						smarker.push(NewMarker);
						map.addLayer(smarker[i]);
						i++;
					});
				}
				if (result.s_var == 2){
					s_coord = new Array();
					s_poly = new Array();
					var i = 0;
					jQuery(result.ar_points).each(function(){
						s_coord.length = 0;
						jQuery(result.ar_points[i]).each(function(){
							var latlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
							s_coord.push(latlng);
						});
					alert (s_coord.join ("\n"));

					s_poly[i] = L.polyline(s_coord,{color: "darkviolet", weight: 4, smoothFactor: 1, opacity: 0.7}).addTo(map);
					i++;
					});
				}
				var maxlat = result.maxlat;
				var maxlon = result.maxlon;
				var minlat = result.minlat;
				var minlon = result.minlon;
				if (maxlat >0 && minlat >0 && maxlon >0  && minlon >0){
				if (maxlat == minlat && maxlon == minlon){
					map.setView(new L.LatLng(maxlat,maxlon), 10);
				}else{
					map.fitBounds([[maxlat, maxlon],[minlat, minlon]]);
				}
				}else{
					map.setView(new L.LatLng(lats,lons), 14);
				}
			}
		},
		"json"
	);
}

$(function(){
    var availableTags = ["дом искусств","банкомат","аудитория","банк","бар","скамейка","велопарковка","прокат велосипедов","бордель","обмен валют","автобусная станция","кафе","аренда автомобиля","прокат автомобиля","автомойа","казино","кинотеатр","поликлиника","клуб","колледж","общественный центр","помещение суда","крематорий","стоматология","врач","общежитие","питьевая вода","автошкола","посольство","телефон экстренных служб","палатка с едой","паромная станция","пожарный гидрант","пожарная станция","фонтан","заправка","кладбище","тренажерный зал","холл","оздоровительный центр","больница","отель","охотничья вышка","мороженное","детский сад","библиотека","магазин","рыночная площадь","горная спасательная служба","ночной клуб","пансионат","дом престарелых","офис","парк","стоянка","аптека","место поклонения","полиция","почтовый ящик","почтовое отделение","дошкольное учреждение","тюрьма","паб","общественное здание","городской рынок","приемная","место утилизации","ресторан","дом престарелых","сауна","школа","укрытие","магазин","торговый центр","сообщество","студия","супермаркет","такси","телефон","театр","туалет","городская администрация","университет","торговый автомат","ветеринарная клиника","усадьба","мусорный бак","wi-fi","молодежный центр","административная граница"];
    $("#scont" ).autocomplete({
      source: availableTags
    });
});

function s_context(){
var tags = jQuery('#scont').val();
if (tags == ''){
	alert ("Заполните поле 'Ключевое слово'");
	$("#scont").focus();
	return false;
}
if (map.getZoom() < 12){
	alert ("Слишком большая область поиска, пожалуйста увеличте zoom как минимум до 12");
	return false;
}
	tags = tags.toLowerCase();
	var bounds = map.getBounds();
	var min = bounds.getSouthWest();
	var max = bounds.getNorthEast();
	var b_minlat = min.lat;
	var b_minlon = min.lng;
	var b_maxlat = max.lat;
	var b_maxlon = max.lng;
	var url = 'get_search.php';
	$("#loader").css("display","block");
	setTimeout(hideloader, 30000);
	jQuery.post(
		url,
		"tags=" + tags + "&minlat=" + b_minlat + "&minlon=" + b_minlon + "&maxlat=" + b_maxlat + "&maxlon=" + b_maxlon,
		function (result){
			$("#loader").css("display","none");
			if (result.type == 'error'){
				alert(result.msg);
			}
			else{
				if (result.s_var == 1){
					smarker = new Array();
					var i = 0;
					jQuery(result.ar_points).each(function(){
						var NewMarker = new L.marker([jQuery(this).attr('lat'), jQuery(this).attr('lon')],{title: sity}).bindPopup("<b>"+sity+"</b><br/>"+jQuery(this).attr('reg')+", "+jQuery(this).attr('dist'));
						smarker.push(NewMarker);
						map.addLayer(smarker[i]);
						i++;
					});
				}
				if (result.s_var == 2){
					s_coord = new Array();
					var i = 0;
					jQuery(result.ar_points).each(function(){
						s_coord.length = 0;
						jQuery(result.ar_points[i]).each(function(){
							var latlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
							s_coord.push(latlng);
						});
//					alert (s_coord.join ("\n"));

					s_poly = L.polyline(s_coord,{color: "darkviolet", weight: 4, smoothFactor: 1, opacity: 0.7}).addTo(map);
					i++;
					});
				}
				if (result.s_var == 3){
					smarker = new Array();
					var i = 0;
					jQuery(result.ar_points1).each(function(){
						var NewMarker = new L.marker([jQuery(this).attr('lat'), jQuery(this).attr('lon')],{title: tags}).bindPopup("<b>"+jQuery(this).attr('name')+"</b><br/>"+jQuery(this).attr('adr')+", "+jQuery(this).attr('nhs'));
						smarker.push(NewMarker);
						map.addLayer(smarker[i]);
						i++;
					});

					s_coord = new Array();
					var i = 0;
					jQuery(result.ar_points2).each(function(){
						s_coord.length = 0;
						jQuery(result.ar_points2[i]).each(function(){
							var latlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
							s_coord.push(latlng);
						});
//					alert (s_coord.join ("\n"));

					s_poly = L.polyline(s_coord,{color: "darkviolet", weight: 4, smoothFactor: 1, opacity: 0.7}).addTo(map);
					i++;
					});
				}

				var maxlat = result.maxlat;
				var maxlon = result.maxlon;
				var minlat = result.minlat;
				var minlon = result.minlon;
				if (maxlat == minlat && maxlon == minlon){
					map.setView(new L.LatLng(maxlat,maxlon), 10);
				}else{
					map.fitBounds([[maxlat, maxlon],[minlat, minlon]]);
				}
			}
		},
		"json"
	);
}

function sh_sub(num){
	if ($('#sub_'+num).is(':visible')){
		$('#sub_'+num).css("display","none");		
	}else{
		$('#sub_'+num).css("display","block");
	}
}

function sferm(){
	var fkoord = jQuery('#ferm_id').immybox('getValue')+'';
	if (fkoord == ''){
		alert ('Выберите ферму');
	}else{
	var fermsplit = fkoord.split(',');
	var sclatlng = L.latLng(fermsplit[0],fermsplit[1]);
	map.setView(sclatlng, 14);
}
}