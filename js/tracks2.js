function osmtr(ver,filetr){
	if (ver == 1){
		var su_name = jQuery('#u_id').val();
		var car = jQuery('#car_id').val();
		var sdate = jQuery('#sour').val();
		var podate = jQuery('#dour').val();
		var pcar = jQuery('#plancars').val();
		var smena = jQuery('#n_sm').val();
		if (jQuery('#all_day').is(':checked')){
			var alld = 1;
		}
		else{
			var alld = 0;
		}
		if (jQuery('#show_tpl').is(':checked')){
			var gr_topl = 1;
		}else{
			var gr_topl = 0;
		}
		if (jQuery('#show_tmpr').is(':checked')){
			var gr_tmpr = 1;
		}else{
			var gr_tmpr = 0;
		}
		var dt1 = sdate.substr(6,4)+'-'+sdate.substr(3,2)+'-'+sdate.substr(0,2)+'T'+sdate.substr(11,5)+':00.000+03:00';
		var dt2 = podate.substr(6,4)+'-'+podate.substr(3,2)+'-'+podate.substr(0,2)+'T'+podate.substr(11,5)+':00.000+03:00';
		
		if (dt1 > dt2){
			alert ('Проверьте правильность введённого интервала дат');
			return(false);
		}
		if (car == ''){
			alert('Не выбран автомобиль');
			jQuery('#car_id').focus();
			return(false);
		}

		var trtype = jQuery('#typetrack').val();
	}
	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');

	var url = 'get_tracks.php';

	jQuery.get(
		url,
		"su_name=" + su_name + "&car=" + car + "&sdt=" + sdate + "&podt=" + podate + "&smena=" + smena + "&allday=" + alld + "&trtype=" + trtype + "&ver=" + ver + "&ftr=" + filetr + "&topl=" + gr_topl + "&tmpr=" + gr_tmpr,
		function (result){
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert('Ошибка!!! Нет данных для текущей даты.');
				return(false);
			}
			OSRemTrackOnMap();
			if ((result.type == 0)||(result.type == 2)){	//	Фактический трек
				var coords = new Array();
				var text = new Array();
				coords.length = 0;
				text.length = 0;
				jQuery(result.track).each(function() {
					var latlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					coords.push(latlng);
					text.push(jQuery(this).attr('text'));
				});
				var tcolor = jQuery('#colortrack').val();
				var car = jQuery('#car_id').val();
				f_poly = L.polyline(coords,{color: tcolor, weight: 4, opacity: 0.7}).addTo(map);
			    d_poly = L.polylineDecorator(f_poly, {
			        patterns: [
            			{offset: 25, repeat: 80, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {color: tcolor, fillOpacity: 0.8, weight: 0}})}
        			]
    			}).addTo(map);
        		f_poly.addEventListener('click', function(e) {
            		var index = _getClosestPointIndex(e.latlng, coords);
            		var popup = L.popup()
                		.setLatLng(new L.latLng(coords[index]))
                		.setContent(text[index])
                		.openOn(self.map);
				});
				if (result.type == 0) map.fitBounds(f_poly.getBounds());
	
				jQuery('#progress').html('');

				if (window.fl_marker){
					for(i=0;i<fl_marker.length;i++){
						map.removeLayer(fl_marker[i]);
					}
				}
				fl_marker = new Array();
				var f_latlng = L.latLng(result.f_lat, result.f_lon);
				var l_latlng = L.latLng(result.l_lat, result.l_lon);
				var f_m = new L.marker(f_latlng,{icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'blue', prefix: 'fa', html: 'Н'}),riseOnHover: true}).bindPopup(result.f_txt);
				fl_marker.push(f_m);
				var l_m = new L.marker(l_latlng,{icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'blue', prefix: 'fa', html: 'K'}),riseOnHover: true}).bindPopup(result.l_txt);
				fl_marker.push(l_m);
				map.addLayer(fl_marker[0]);map.addLayer(fl_marker[1]);

		        $("#inforoute").toast({
		            autohide: false
        		});
        		$("#insinforoute").html('<div class="h6">'+car+' - фактический маршрут<br>' + result.dop_inf + 
        			'</div><ul class="nav nav-tabs"><li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#main">Основное</a></li><li class="nav-item"><a class="nav-link" data-toggle="tab" href="#detail">Дополнительно</a></li></ul>' 
        			+ result.dop_all);

        		$("#insdist").html(f_poly.measuredDistance());
        		$("#insskor").html(result.maxskor+' км/ч');
        		if (result.type == 0) $("#inforoute").toast('show');	// показываем при выводе только факт. маршрута, при выводе обоих - вывод в плановом

/*				jQuery('#savetr').html('<span class="imit_a" onclick="sendtr()">Сохраниить трек</span><input type="hidden" name="trpath" id="trpath" value="tracks/'+su_name+car+'.gpx"/>');*/

				if (jQuery('#show_stops').prop('checked') == true){
					show_stops();
				}

				if (jQuery('#show_tpl').prop('checked') == true){

		        $("#graph").toast({
		            autohide: false
        		});

				var dataPoints1 = [];
				var dataPoints2 = [];
				var dataPoints3 = [];
				jQuery(result.topl).each(function() {
					var topl_dt =jQuery(this).attr('vremya');
					var topl_yr =jQuery(this).attr('yroven');
					var topl_s_yr =jQuery(this).attr('s_yroven');

					dataPoints1.push({
						x: topl_dt,
						y: topl_yr
					});
					dataPoints2.push({
						x: topl_dt,
						y: topl_s_yr
					});
				});

				jQuery(result.track).each(function() {
					var track_dt =jQuery(this).attr('vremya');
					var track_skor =jQuery(this).attr('skor');

					dataPoints3.push({
						x: track_dt,
						y: track_skor
					});
				});

				var options = {
					zoomEnabled: true,
					exportEnabled: true,
					animationEnabled: true,
					title: {
						text: car
					},
					width: 600,
					height: 300,
					axisY: {
						title: "Топливо, л",
						includeZero: true,
					},
					axisY2: {
						title: "Скорость, км/ч",
					},
        			axisX:{      
            			valueFormatString: "DD.MM HH:mm",
            			crosshair: {
							enabled: true,
							snapToDataPoint: true
						}
        			},
					legend: {
						verticalAlign: "top",
						horizontalAlign: "right",
						dockInsidePlotArea: true
					},
					toolTip: {
						shared: true
					},
					data: [
					{
						type: "splineArea",	
						name: "Скорость",
						color: "rgba(60, 179, 113, 0.6)",
						legendText: "Скорость",
						axisYType: "secondary",
						showInLegend: true,
						dataPoints: dataPoints3
					},
					{
						name: "Топливо (реал)",
						color: "rgba(0, 159, 217, 0.9)",
						showInLegend: true,
						legendMarkerType: "square",
						type: "splineArea",
						xValueType: "dateTime",
						click: onClick,
						dataPoints: dataPoints1,
					},
					{
						name: "Топливо (сглаж)",
						showInLegend: true,
						color: "red",
						legendMarkerType: "square",
						type: "spline",
						xValueType: "dateTime",
						click: onClick,
						dataPoints: dataPoints2,
					}
					]
				};
				var topl_chart = new CanvasJS.Chart("topl_chart", options);
				topl_chart.render();

				$(topl_chart.container).click(function(e) {
					var parentOffset = $(this).parent().offset();
					var relX = e.pageX - parentOffset.left - 12;
  					var click_dt = Math.round(topl_chart.axisX[0].convertPixelToValue(relX));

  					jQuery(result.track).each(function() {
						var track_dt = jQuery(this).attr('vremya');
						if (click_dt <= track_dt){
							gotosub_wm(jQuery(this).attr('lat'),jQuery(this).attr('lon'));
							return false;
						}
					});

				});

        		$("#graph").toast('show');
				}

				if (jQuery('#show_skor').prop('checked') == true){
		        $("#skor").toast({
		            autohide: false
        		});

				var data = [];
				var dataSeries = { type: "area", xValueType: "dateTime", click: onClick};
				var dataPoints = [];

				jQuery(result.track).each(function() {
					var track_dt =jQuery(this).attr('vremya');
					var track_skor =jQuery(this).attr('skor');

					dataPoints.push({
						x: track_dt,
						y: track_skor
					});
				});
				
				dataSeries.dataPoints = dataPoints;
				data.push(dataSeries);


				var options = {
					zoomEnabled: true,
					exportEnabled: true,
					animationEnabled: true,
					title: {
						text: car
					},
					width: 600,
					height: 300,
					axisY: {
						suffix: " км/ч",
					},
        			axisX:{      
            			valueFormatString: "DD-MMM HH:mm",
            			crosshair: {
							enabled: true,
							snapToDataPoint: true
						}
        			},
					data: data
				};
				var skor_chart = new CanvasJS.Chart("skor_chart",options);
				skor_chart.render();

				$(skor_chart.container).click(function(e) {
					var parentOffset = $(this).parent().offset();
					var relX = e.pageX - parentOffset.left - 12;
  					var click_dt = Math.round(skor_chart.axisX[0].convertPixelToValue(relX));

  					jQuery(result.track).each(function() {
						var track_dt = jQuery(this).attr('vremya');
						if (click_dt <= track_dt){
							gotosub_wm(jQuery(this).attr('lat'),jQuery(this).attr('lon'));
							return false;
						}
					});

				});

        		$("#skor").toast('show');
        		}
				if (jQuery('#show_tmpr').prop('checked') == true){
					$("#tmpr").toast({
						autohide: false
					});
	
					var data = [];
					var dataSeries = { type: "area", xValueType: "dateTime", click: onClick};
					var dataPoints = [];
	
					jQuery(result.track).each(function() {
						var track_dt =jQuery(this).attr('vremya');
						var track_tmpr =jQuery(this).attr('tmpr');

						if (track_tmpr > -30 && track_tmpr < 70){
							dataPoints.push({
								x: track_dt,
								y: track_tmpr
							});
						}
					});
					
					dataSeries.dataPoints = dataPoints;
					data.push(dataSeries);
	
	
					var options = {
						zoomEnabled: true,
						exportEnabled: true,
						animationEnabled: true,
						title: {
							text: car
						},
						width: 600,
						height: 300,
						axisY: {
							suffix: " °C",
						},
						axisX:{      
							valueFormatString: "DD-MMM HH:mm",
							crosshair: {
								enabled: true,
								snapToDataPoint: true
							}
						},
						data: data
					};
					var tmpr_chart = new CanvasJS.Chart("tmpr_chart", options);
					tmpr_chart.render();
	
					$(tmpr_chart.container).click(function(e) {
						var parentOffset = $(this).parent().offset();
						var relX = e.pageX - parentOffset.left - 12;
						  var click_dt = Math.round(tmpr_chart.axisX[0].convertPixelToValue(relX));
	
						  jQuery(result.track).each(function() {
							var track_dt = jQuery(this).attr('vremya');
							if (click_dt <= track_dt){
								gotosub_wm(jQuery(this).attr('lat'),jQuery(this).attr('lon'));
								return false;
							}
						});
	
					});
	
					$("#tmpr").toast('show');
				}
				function onClick(e) {
					var p_num = e.dataPointIndex;
					console.log(e.dataPoint.x);
					var tmp_c_lat = Object.values(jQuery(result.track[p_num]['lat']));
					var tmp_c_lon = Object.values(jQuery(result.track[p_num]['lon']));
					var tmp_lat = tmp_c_lat[3];
					var tmp_lon = tmp_c_lon[3];
					gotosub_wm(tmp_lat,tmp_lon);
				}

			}
			if ((result.type == 1)||(result.type == 2)){	//	Плановый трек

				var pcoords = new Array();
				var ptr_time = new Array();
				pcoords.length = 0;
				ptr_time.length = 0;
				jQuery(result.ptrack).each(function(){
					var platlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					pcoords.push(platlng);
					ptr_time.push(jQuery(this).attr('date'));
				});
				var car = jQuery('#car_id').val();

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
                		.setContent('<b>'+car+'</b> - плановый маршрут<br/><b>дата и время:</b> '+ptr_time[index])
                		.openOn(self.map);
				});

				plan_stops_LayerGroup = L.layerGroup();						// Плановые стоянки
				pmarker = new Array();
				var i = 0;
				var infoplstops = "";
				var infoplstops_k = "";
				jQuery(result.pstops).each(function(){
					var pslatlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					var pm = new L.marker(pslatlng,{icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'orange', prefix: 'fa', html: jQuery(this).attr('num')}),zIndexOffset:10}).bindPopup(jQuery(this).attr('text')+jQuery(this).attr('adr'));
					pmarker.push(pm);
					map.addLayer(pmarker[i]);
					plan_stops_LayerGroup.addLayer(pmarker[i]);
					i++;

					if (jQuery(this).attr('lat') && jQuery(this).attr('lon')){
						var infoplstops_k = "onclick='gotosub("+jQuery(this).attr('lat')+","+jQuery(this).attr('lon')+")'";
					}else{
						var infoplstops_k = "";
					}
					
					infoplstops += "<div class='my-1 rlabel' "+infoplstops_k+"><b>"+i+"</b> "+jQuery(this).attr('adr')+"</div>"
				});
				plan_stops_LayerGroup.addTo(map); layerControl.addOverlay(plan_stops_LayerGroup, 'Плановые стоянки '+car);
				if (result.type == 1) map.fitBounds(p_poly.getBounds());
				if (result.type == 2) map.fitBounds([[result.maxlat, result.maxlon],[result.minlat, result.minlon]]);
				
				var infoplroute = '<div class="h6 mb-2">'+car+' - плановый маршрут</div>';
				if (result.type == 1){
		        	$("#inforoute").toast({
		            	autohide: false
        			});
        			$("#insinforoute").html(infoplroute+"<div class='pltrinfo overflow-auto'>"+infoplstops+"</div>");
				}else{
					$("#insinforoute").append(infoplroute+"<div class='pltrinfo overflow-auto'>"+infoplstops+"</div>");
				}
       			$("#inforoute").toast('show');

				jQuery('#progress').html('');
			}
		},
		"json"
	);
}

function gotosub(lat,lon){
	map.setView(new L.LatLng(lat, lon),15);
}


function gotosub_wm(lat,lon){
	if (window.skor_marker){
		map.removeLayer(skor_marker)
	}
    skor_marker = new L.Marker(new L.LatLng(lat, lon));
    map.addLayer(skor_marker);
	map.setView(new L.LatLng(lat, lon),15);
}

function playnow(){
	if (jQuery('#hidpause').val() == 'off' || jQuery('#hidpause').val() == 'stop'){
	var su_name = jQuery('#u_id').val();
	var car = jQuery('#car_id').val();
	var _url = "tracks/"+su_name+car+".xml";

	if (car == ''){
		alert('Не выбран автомобиль');
		return(false);
	}
	if (window.lgpx == undefined){
		alert('Не загружен маршрут');
		return(false);
	}

	var url = 'get_ren.php';

	jQuery.get(
		url,
		"&su_name=" + su_name + "&car=" + car,
		function (result){
			if (result.type == 'error'){
				alert ("Ошибка воспроизведения");
				return(false);
			}
			else{
				jQuery('#playon').attr('src','images/playon.png');
				jQuery('#stopon').attr('src','images/stopoff.png');
				jQuery('#hidpause').attr('value','play');
				if (window.playmarkers !== undefined){
					map.removeLayer(playmarkers,true);
				}
    			playmarkers = new OpenLayers.Layer.Markers("PlayMarkers");
				map.addLayer(playmarkers);
				arcount = 0;
				(function(){
				if (arcount < result.arlat.length){
				lonLat = new OpenLayers.LonLat(result.arlon[arcount], result.arlat[arcount]).transform(
				new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
				map.getProjectionObject() // to Spherical Mercator Projection
				);
    			if (window.playmarkers !== undefined) playmarkers.clearMarkers();
				playmarkers.addMarker(new OpenLayers.Marker(lonLat));
				if (jQuery('#hidpause').val() == 'play')	arcount++;
				if (jQuery('#hidpause').val() == 'stop')	arcount = result.arlat.length;
				var speed = jQuery('#izmkskor').val();
				setTimeout(arguments.callee, speed);
				}else{
        			if (window.playmarkers !== undefined) playmarkers.clearMarkers();
        			jQuery('#playon').attr('src','images/playoff.png');
        			jQuery('#pauseon').attr('src','images/pauseoff.png');
					jQuery('#stopon').attr('src','images/stopon.png');
   					jQuery('#hidpause').attr('value','off');
					return (false);
				}
				})();
	    	}
		},
		"json"
	);
	}else{
		if (jQuery('#hidpause').val() == 'pause'){
			jQuery('#hidpause').attr('value','play');
			jQuery('#pauseon').attr('src','images/pauseoff.png');
		}
	}
}

function pause(){
	if (jQuery('#hidpause').val() == 'play'){
		jQuery('#hidpause').attr('value','pause');
		jQuery('#pauseon').attr('src','images/pauseon.png');
	}else{
		if (jQuery('#hidpause').val() == 'pause'){
			jQuery('#hidpause').attr('value','play');
			jQuery('#pauseon').attr('src','images/pauseoff.png');
		}
	}
}

function playstop(){
	jQuery('#hidpause').attr('value','stop');

}

function show_stops(){
	var su_name = jQuery('#u_id').val();
	var car = jQuery('#car_id').val();
	var sdate = jQuery('#sour').val();
	var podate = jQuery('#dour').val();
	var f_gwx = 'stops';
	if (jQuery('#all_day').prop('checked') == true){
		var alld = 1;
	}
	else{
		var alld = 0;
	}
	if (car == ''){
		alert('Не выбран автомобиль');
		jQuery('#car_id').focus();			
		return(false);
	}
	var dt1 = sdate.substr(6,4)+'-'+sdate.substr(3,2)+'-'+sdate.substr(0,2)+'T'+sdate.substr(11,5)+':00.000+03:00';
	var dt2 = podate.substr(6,4)+'-'+podate.substr(3,2)+'-'+podate.substr(0,2)+'T'+podate.substr(11,5)+':00.000+03:00';
	
	if (dt1 > dt2){
		alert ('Проверьте правильность введённого интервала дат');
		return(false);
	}
	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');

	var url = 'get_gwx.php';

	jQuery.get(
		url,
		"&su_name=" + su_name + "&car=" + car + "&sdt=" + sdate + "&podt=" + podate + "&f_gwx=" + f_gwx + "&allday=" + alld,
		function (result){
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert (result.res);
				return(false);
			}
			else{
				if (window.fmarker){
					for(i=0;i<fmarker.length;i++){
						map.removeLayer(fmarker[i]);
						layerControl.removeLayer(fact_stops_LayerGroup);
					}
				}
				fact_stops_LayerGroup = L.layerGroup();
				fmarker = new Array();
				var i = 0;
				jQuery(result.fstops).each(function(){
					var fslatlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					var fm = new L.marker(fslatlng,{icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'orange', prefix: 'fa', html: 'C'}),riseOnHover: true}).bindPopup(jQuery(this).attr('text'));
					fmarker.push(fm);
					map.addLayer(fmarker[i]);
					fact_stops_LayerGroup.addLayer(fmarker[i]);
					i++;
				});
				fact_stops_LayerGroup.addTo(map); layerControl.addOverlay(fact_stops_LayerGroup, 'Фактические стоянки '+car);
				jQuery('#progress').html('');
			}
		},
		"json"
	);
};

jQuery(document).ready(function (){

	jQuery('#previd').click(function(){
		var su_name = jQuery('#u_id').val();
		var car = jQuery('#car_id').val();
		var sdate = jQuery('#sour').val();
		var podate = jQuery('#dour').val();
		var sskor = jQuery('#sskor').val();

		if (jQuery('#pskor').prop('checked') == true){
			var skor = 1;
		}
		else{
			var skor = 0;
		}
		if (jQuery('#all_day').prop('checked') == true){
			var alld = 1;
		}
		else{
			var alld = 0;
		}

		if (car == ''){
			alert('Не выбран автомобиль');
			return(false);
		}
		if ((skor == 0)/*&&(temp == 0)*/){
			alert('Не выбран ни один параметр (скорость, температура)');
			return(false);			
		}
		/*
		jQuery('#prevcontrol').slideToggle(250);
		*/
		var url = 'get_prev.php';

		jQuery.get(
			url,
			"&su_name=" + su_name + "&car=" + car + "&sdt=" + sdate + "&podt="  + podate + "&allday=" + alld + "&skor=" + skor + "&sskor=" + sskor,
			function (result){
				if (result.type == 'error'){
					alert ("Превышений не найдено");
					jQuery('#prev').css('border','0');
					return(false);
				}
				else{
					if (window.prevmarker){
						for(i=0;i<prevmarker.length;i++){
							map.removeLayer(prevmarker[i]);
							layerControl.removeLayer(prev_LayerGroup);
						}
					}
					prev_LayerGroup = L.layerGroup();
					prevmarker = new Array();
					var i = 0;
					jQuery(result.prevs).each(function(){
						var prevlatlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
						var prevm = new L.marker(prevlatlng,{icon: L.AwesomeMarkers.icon({icon: '', markerColor: 'black', prefix: 'fa', html: '!'}),riseOnHover: true}).bindPopup(jQuery(this).attr('text'));
						prevmarker.push(prevm);
						map.addLayer(prevmarker[i]);
						prev_LayerGroup.addLayer(prevmarker[i]);
						i++;
					});
					jQuery('#prev').css('border','0');
					prev_LayerGroup.addTo(map); layerControl.addOverlay(prev_LayerGroup, 'Превышения скорости '+car);
				}
			},
			"json"
		);
});

});


// Очистить карту
function OSRemTrackOnMap(){
	if (window.f_poly){
		map.removeLayer(f_poly);
	}
	if (window.d_poly){
		map.removeLayer(d_poly);
	}
	if (window.p_poly){
		map.removeLayer(p_poly);
	}
	if (window.pd_poly){
		map.removeLayer(pd_poly);
	}
	if (window.r_poly){
		map.removeLayer(r_poly);
	}
	if (window.pmarker){
		for(i=0;i<pmarker.length;i++){
			map.removeLayer(pmarker[i]);
			layerControl.removeLayer(plan_stops_LayerGroup);
		}
	}
	if (window.fmarker){
		for(i=0;i<fmarker.length;i++){
			map.removeLayer(fmarker[i]);
			layerControl.removeLayer(fact_stops_LayerGroup);
		}
	}
	if (window.fl_marker){
		for(i=0;i<fl_marker.length;i++){
			map.removeLayer(fl_marker[i]);
		}
	}
}

function OSRemAllFromMap(){
	$("#inforoute").toast('hide');
	$("#graph").toast('hide');
	$("#skor").toast('hide');
	$("#findcontrol").toast('hide');
	$("#prevcontrol").toast('hide');
	$("#roadscontrol").toast('hide');
	
	if (window.lcmarker){
		for(i=0;i<lcmarker.length;i++){
			map.removeLayer(lcmarker[i]);
		}
	}
	if (window.carsmarker){
		for(i=0;i<carsmarker.length;i++){
			map.removeLayer(carsmarker[i]);
		}
		layerControl.removeLayer(cars_LayerGroup);
		delete carsmarker;
	}
	if (window.fermsmarker){
		for(i=0;i<fermsmarker.length;i++){
			map.removeLayer(fermsmarker[i]);
		}
		layerControl.removeLayer(ferms_LayerGroup);
		delete fermsmarker;
	}
	if (window.scmarker){
		for(i=0;i<scmarker.length;i++){
			map.removeLayer(scmarker[i]);
		}
	}
	if (window.prevmarker){
		for(i=0;i<prevmarker.length;i++){
			map.removeLayer(prevmarker[i]);
			layerControl.removeLayer(prev_LayerGroup);
		}
	}
	if (window.fl_marker){
		for(i=0;i<fl_marker.length;i++){
			map.removeLayer(fl_marker[i]);
		}
	}
	if (window.repmarker){
		for(i=0;i<repmarker.length;i++){
			map.removeLayer(repmarker[i]);
		}
	}
	if (window.skor_marker){
		map.removeLayer(skor_marker);
	}
	if (window.grmarker){
		for(i = 0; i < grmarker.length; i++){
			map.removeLayer(grmarker[i]);
		}
		delete grmarker;
	}
	OSRemTrackOnMap();
}