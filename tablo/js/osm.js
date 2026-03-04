function init(){
var localUrl = 'https://cis-tile.savushkin.by/osm_tiles/{z}/{x}/{y}.png';
var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';

var local_osm = L.tileLayer(localUrl, {attribution: osmAttrib, maxZoom: 18}), inet_osm  = L.tileLayer(osmUrl, {attribution: osmAttrib, maxZoom: 18});

map = L.map('map', {
			center: [53.85, 27.50],
			zoom: 8,
			doubleClickZoom: false,
/*			renderer : labelTextCollision,*/			
			layers: [local_osm]
		});

L.control.scale().addTo(map);

var baseLayers = {
			"Локальная карта OSM": local_osm,
			"Карта OSM": inet_osm
		};

layerControl = L.control.layers(baseLayers).addTo(map);
}

_getClosestPointIndex = function(lPoint, arrayLPoints) {
	var distanceArray = [];
	for ( var i = 0; i < arrayLPoints.length; i++ ) {
		distanceArray.push( lPoint.distanceTo(arrayLPoints[i]) );
	}
	return distanceArray.indexOf(  Math.min.apply(null, distanceArray) );
}

function show_plrtack(anum, dt_beg, stops) {
    var url = 'get_tracks.php';
    jQuery.post(
        url,
        "car=" + anum + "&date_b=" + dt_beg + "&stops=" + stops,
        function (result) {
            if (result.type == 'error') {
                alert(result.msg);
                return false;
            } else {
                console.log(result);
                
                // Удаляем старые полилинии
                if (window.p_poly) {
                    console.log('Удаляем старые треки');
                    for (var i = 0; i < window.p_poly.length; i++) {
                        if (window.p_poly[i]) {
                            map.removeLayer(window.p_poly[i]);
                        }
                    }
                    delete window.p_poly;
                }
                
                // Удаляем старые декораторы
                if (window.pd_poly) {
                    for (var i = 0; i < window.pd_poly.length; i++) {
                        if (window.pd_poly[i]) {
                            map.removeLayer(window.pd_poly[i]);
                        }
                    }
                    delete window.pd_poly;
                }
                
                // Удаляем старые маркеры остановок
                if (window.stopmarker) {
                    for (var i = 0; i < window.stopmarker.length; i++) {
                        if (window.stopmarker[i]) {
                            map.removeLayer(window.stopmarker[i]);
                        }
                    }
                    delete window.stopmarker;
                }
                
                // Удаляем старые маркеры
                if (window.rmarker) {
                    for (var i = 0; i < window.rmarker.length; i++) {
                        if (window.rmarker[i]) {
                            map.removeLayer(window.rmarker[i]);
                        }
                    }
                    delete window.rmarker;
                }
                
                // Создаём новые глобальные переменные
                var pcoords = [];
                var ptr_time = [];
                window.p_poly = [];
                window.pd_poly = [];
                
                pcoords.length = 0;
                ptr_time.length = 0;
                
                var tracks = result.tracks;
                let color = ['black', 'brown', 'grey'];
                
                for (let i = 0; i < tracks.length; i++) {
                    // Очищаем массивы для каждого трека
                    pcoords = [];
                    ptr_time = [];
                    
                    jQuery(tracks[i]).each(function() {
                        var platlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
                        pcoords.push(platlng);
                        ptr_time.push(jQuery(this).attr('date'));
                    });
                    
                    window.p_poly[i] = L.polyline(pcoords, {color: color[i], weight: 4, opacity: 0.7}).addTo(map);
                    
                    window.pd_poly[i] = L.polylineDecorator(window.p_poly[i], {
                        patterns: [
                            {offset: 25, repeat: 80, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {color: color[i], fillOpacity: 0.8, weight: 0}})}
                        ]
                    }).addTo(map);
                    
                    // Сохраняем pcoords и ptr_time для использования в обработчике клика
                    let currentPcoords = [...pcoords];
                    let currentPtr_time = [...ptr_time];
                    
                    window.p_poly[i].addEventListener('click', function(e) {
                        var index = _getClosestPointIndex(e.latlng, currentPcoords);
                        var p_popup = L.popup()
                            .setLatLng(new L.latLng(currentPcoords[index]))
                            .setContent('<b>' + anum + '</b> - плановый маршрут<br/><b>дата и время:</b> ' + currentPtr_time[index])
                            .openOn(map);
                    });
                    
                    map.fitBounds(window.p_poly[i].getBounds());
                    
                    // Работа с остановками
                    var stops_LayerGroup = L.layerGroup();
                    window.stopmarker = window.stopmarker || [];
                    var stopcoords = [];
                    var i2 = 0;
                    
                    if (i == 0) {
                        var ins_info = "<div class='top_z' onclick='sh_list()'>" + anum + "</div><div id='stops_list'>";
                        $('#info').html("");
                    }
                    
                    jQuery(result.pstops[i]).each(function() {
                        if (jQuery(this).attr('stat') > '') {
                            var statmarker = jQuery(this).attr('stat').charAt(i2);
                        } else {
                            var statmarker = 0;
                        }
                        
                        var stoplatlon = new L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
                        
                        if (statmarker == 1) {
                            var stopsm = new L.marker(stoplatlon, {riseOnHover: true}).bindPopup(jQuery(this).attr('text'));
                            ins_info += "<div class='blue' onclick='go_to_stop(" + jQuery(this).attr('lat') + "," + jQuery(this).attr('lon') + ")'>" + jQuery(this).attr('adr') + "</div>";
                        } else {
                            var stopsm = new L.marker(stoplatlon, {
                                riseOnHover: true, 
                                icon: L.icon({
                                    iconUrl: 'images/marker-red.png', 
                                    iconSize: [25, 41], 
                                    iconAnchor: [12, 41], 
                                    popupAnchor: [1, -34], 
                                    shadowUrl: 'images/marker-shadow.png', 
                                    shadowSize: [41, 41]
                                })
                            }).bindPopup(jQuery(this).attr('text'));
                            ins_info += "<div class='red' onclick='go_to_stop(" + jQuery(this).attr('lat') + "," + jQuery(this).attr('lon') + ")'>" + jQuery(this).attr('adr') + "</div>";
                        }
                        
                        window.stopmarker.push(stopsm);
                        stopcoords.push(stoplatlon);
                        map.addLayer(window.stopmarker[window.stopmarker.length - 1]);
                        i2++;
                    });
                }
                
                ins_info += "</div>";
                $('#info').html(ins_info);
                
                // Работа с дополнительным маркером
                if (result.r_lat > '' && result.r_lon > '') {
                    window.rmarker = [];
                    var r_latlon = L.latLng(result.r_lat, result.r_lon);
                    var rm = new L.marker(r_latlon, {
                        zIndexOffset: 10000, 
                        riseOnHover: true, 
                        icon: L.icon({
                            iconUrl: 'images/marker-green.png', 
                            iconSize: [25, 41], 
                            iconAnchor: [12, 41], 
                            popupAnchor: [1, -34], 
                            shadowUrl: 'images/marker-shadow.png', 
                            shadowSize: [41, 41]
                        })
                    }).bindPopup(result.r_text);
                    
                    window.rmarker.push(rm);
                    map.addLayer(window.rmarker[0]);
                }
            }
        },
        "json"
    );
}

function go_to_stop(lat,lon){
	map.setView(new L.LatLng(lat, lon),16);
}

function sh_list(){
	if ($('#stops_list').css('display') == 'none'){
		$('#stops_list').css('display','block');
	}else{
		$('#stops_list').css('display','none');
	}
}