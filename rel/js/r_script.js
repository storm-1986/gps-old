function init(){

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

var localUrl = 'http://cis-tile.savushkin.by/osm_tiles/{z}/{x}/{y}.png';
var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var osmAttrib = 'Savushkin';

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

tom();
}

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

function sh_sub(num){
	if ($('#sub_'+num).is(':visible')){
		$('#sub_'+num).css("display","none");		
	}else{
		$('#sub_'+num).css("display","block");
	}
}