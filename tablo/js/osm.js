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

function show_plrtack(anum,dt_beg,stops){
	var url = 'get_tracks.php';
		jQuery.post(
			url,
			"car=" + anum + "&date_b=" + dt_beg + "&stops=" + stops,
			function (result){
				if (result.type == 'error'){
					alert(result.msg);
					return(false);
				}
				else{
				if (window["p_poly"]) map.removeLayer(window["p_poly"]);
				if (window["pd_poly"]) map.removeLayer(window["pd_poly"]);
				if (window.stopmarker){
					for(i=0;i<stopmarker.length;i++){
						map.removeLayer(stopmarker[i]);
					}
					delete stopmarker;
				}
				if (window.rmarker){
					for(i=0;i<rmarker.length;i++){
						map.removeLayer(rmarker[i]);
					}
				}
				var pcoords = new Array();
				var ptr_time = new Array();
				pcoords.length = 0;
				ptr_time.length = 0;
				jQuery(result.tracks).each(function(){
					var platlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					pcoords.push(platlng);
					ptr_time.push(jQuery(this).attr('date'));
				});

				p_poly = L.polyline(pcoords,{color: 'black', weight: 4, opacity: 0.7}).addTo(map);
			    pd_poly = L.polylineDecorator(p_poly, {
			        patterns: [
            			{offset: 25, repeat: 80, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {color: 'black', fillOpacity: 0.8, weight: 0}})}
        			]
    			}).addTo(map);
        		p_poly.addEventListener('click', function(e) {
            		var index = _getClosestPointIndex(e.latlng, pcoords);
            		var p_popup = L.popup()
                		.setLatLng(new L.latLng(pcoords[index]))
                		.setContent('<b>'+anum+'</b> - плановый маршрут<br/><b>дата и время:</b> '+ptr_time[index])
                		.openOn(self.map);
				});
				map.fitBounds(p_poly.getBounds());
				
				stops_LayerGroup = L.layerGroup();
				stopmarker = new Array();
				stopcoords = new Array();
				var i = 0;
				var ins_info = "<div class='top_z' onclick='sh_list()'>"+anum+"</div><div id='stops_list'>";
				$('#info').html("");
				jQuery(result.pstops).each(function(){
					if (jQuery(this).attr('stat') > ''){
						var statmarker = jQuery(this).attr('stat').charAt(i);
					}else{
						var statmarker = 0;
					}
					var stoplatlon = new L.latLng(jQuery(this).attr('lat'),jQuery(this).attr('lon'));
					if (statmarker == 1){
						var stopsm = new L.marker(stoplatlon,{riseOnHover: true}).bindPopup(jQuery(this).attr('text'));
						ins_info += "<div class='blue' onclick='go_to_stop("+jQuery(this).attr('lat')+","+jQuery(this).attr('lon')+")'>"+jQuery(this).attr('adr')+"</div>";
					}else{
						var stopsm = new L.marker(stoplatlon,{riseOnHover: true, icon:L.icon({iconUrl: 'images/marker-red.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowUrl: 'images/marker-shadow.png', shadowSize: [41, 41]})}).bindPopup(jQuery(this).attr('text'));
						ins_info += "<div class='red' onclick='go_to_stop("+jQuery(this).attr('lat')+","+jQuery(this).attr('lon')+")'>"+jQuery(this).attr('adr')+"</div>";						
					}
					stopmarker.push(stopsm);
					stopcoords.push(stoplatlon);
					map.addLayer(stopmarker[i]);
					i++;
				});
				ins_info += "</div>";
				$('#info').html(ins_info);
				if (result.r_lat > '' && result.r_lon > ''){
					rmarker = new Array();
					var r_latlon = L.latLng(result.r_lat, result.r_lon);
					var rm = new L.marker(r_latlon,{zIndexOffset: 10000, riseOnHover: true, icon:L.icon({iconUrl: 'images/marker-green.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowUrl: 'images/marker-shadow.png', shadowSize: [41, 41]})}).bindPopup(result.r_text);
					rmarker.push(rm);
					map.addLayer(rmarker[0]);
				}
/*				map.setView(sclatlng, 16);*/
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