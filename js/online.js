/*
function fonline(){
	var setint = jQuery('#sint').val();
	var su_name = jQuery('#u_id').val();
	if (jQuery("#mash").prop("checked")){
		mash = 1;
	}else{
		mash = 0;
	}
	if (jQuery("#mvoz").prop("checked")){
		mvoz = 1;
	}else{
		mvoz = 0;
	}
	if (mash == 0 && mvoz == 0){
		alert ('Не выбрано ни одной группы автомобилей');
		return false;
	}
	var url = 'get_cars.php';

	jQuery.get(
		url,
		"su_name=" + su_name + "&flagrep=2&fmash=" + mash + "&fmvoz=" + mvoz,
		function (result) {
			if (result.type == 'error') {
				alert('Нет данных для отображения');
				return(false);
			}
			else {
				var hid1 = '';
				jQuery(result.regions).each(function(){
					hid1 += '<tr><th scope="row">'+jQuery(this).attr('title')+'</th><td>'+jQuery(this).attr('speed')+'</td><td>'+jQuery(this).attr('time')+'</td><td>'+jQuery(this).attr('tel')+'</td></tr>';
				});
				hid1 = '<table class="table table-striped text-center"><thead><tr><th scope="col">Машина</th><th scope="col">Скорость</th><th scope="col">Время последних данных</th><th scope="col">Тел. номер прибора</th></tr></thead>'+hid1+'</table>';
				jQuery('#tpcars').html(hid1);
			}
		},
		"json"
	);

	int1 = window.setInterval(function(){

	jQuery.get(
		url,
		"su_name=" + su_name + "&flagrep=2&fmash=" + mash + "&fmvoz=" + mvoz,
		function (result) {
			if (result.type == 'error') {
				alert('Нет данных для отображения');
				return(false);
			}
			else {
				var hid1 = '';
				jQuery(result.regions).each(function(){
					hid1 += '<tr><th scope="row">'+jQuery(this).attr('title')+'</th><td>'+jQuery(this).attr('speed')+'</td><td>'+jQuery(this).attr('time')+'</td><td>'+jQuery(this).attr('tel')+'</td></tr>';
				});
				hid1 = '<table class="table table-striped text-center"><thead><tr><th scope="col">Машина</th><th scope="col">Скорость</th><th scope="col">Время последних данных</th><th scope="col">Тел. номер прибора</th></tr></thead>'+hid1+'</table>';
				jQuery('#tpcars').html(hid1);
			}
		},
		"json"
	);
	}, setint);

		jQuery('#stop').attr('disabled', false);
		jQuery('#start').attr('disabled', true);
		jQuery('#sint').attr('disabled', true);
		jQuery('#mash').attr('disabled', true);
		jQuery('#mvoz').attr('disabled', true);
};

function fonlinestop(){
	stopint1 = window.clearInterval(int1);
		jQuery('#stop').attr('disabled', true);
		jQuery('#start').attr('disabled', false);
		jQuery('#sint').attr('disabled', false);
		jQuery('#mash').attr('disabled', false);
		jQuery('#mvoz').attr('disabled', false);
};
*/
function autonline(){
	var day = new Date();
	var chas = day.getHours();
	if (chas < 10) chas = '0' + chas;
	var min = day.getMinutes() - 1;
	if (min < 10) min = '0' + min;
	var sec = day.getSeconds();
	if (sec < 10) sec = '0' + sec;
	var fulldt = chas+':'+min+':'+sec;
	var onlcar = jQuery('#car_id').val();
	var setint = jQuery('#onlint').val();
	var su_name = jQuery('#u_id').val();
	var url = 'get_online.php';
	if (onlcar == ''){
		alert ('Введите номер машины');
		return false;
	}
	jQuery.get(
		url,
		"su_name=" + su_name + "&car=" + onlcar + "&dt=" + fulldt,
		function (result){
			if (result.type == 'success'){
				jQuery('#onlinfo').html(onlcar+' '+result.onltime+' Скорость: '+result.spd+' км/ч');
				if (window.onlmarker){
					for(i=0;i<onlmarker.length;i++){
						map.removeLayer(onlmarker[i]);
					}
				}
				onlmarker = new Array();
				var onllatlng = L.latLng(result.lat,result.lon);
				var onlm = new L.marker(onllatlng).bindPopup('<b>'+onlcar+'</b> онлайн<br/>Время: '+result.onltime+'<br/>Скорость: '+result.spd+' км/ч');
				onlmarker.push(onlm);
				map.addLayer(onlmarker[0]);
				map.setView(onllatlng, 14);
			}
		},
		"json"
	);
	
	int1 = window.setInterval(function(){

	jQuery.get(
		url,
		"su_name=" + su_name + "&car=" + onlcar + "&dt=" + fulldt,
		function (result) {
			if (result.type == 'success'){
				jQuery('#onlinfo').html(onlcar+' '+result.onltime+' Скорость: '+result.spd+' км/ч');
				if (window.onlmarker){
					for(i=0;i<onlmarker.length;i++){
						map.removeLayer(onlmarker[i]);
					}
				}
				onlmarker = new Array();
				var onllatlng = L.latLng(result.lat,result.lon);
				var onlm = new L.marker(onllatlng).bindPopup('<b>'+onlcar+'</b> онлайн<br/>Время: '+result.onltime+'<br/>Скорость: '+result.spd+' км/ч');
				onlmarker.push(onlm);
				map.addLayer(onlmarker[0]);

				var coords = new Array();
				var text = new Array();
				coords.length = 0;
				text.length = 0;
				jQuery(result.onl_track).each(function() {
					var latlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					coords.push(latlng);
					text.push(jQuery(this).attr('text'));
				});
				onl_poly = L.polyline(coords,{color: 'blue', weight: 4, opacity: 0.7}).addTo(map);
			    onld_poly = L.polylineDecorator(onl_poly, {
			        patterns: [
            			{offset: 25, repeat: 80, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {color: 'blue', fillOpacity: 0.8, weight: 0}})}
        			]
    			}).addTo(map);
    			$("#infoonlroute").toast({
		            autohide: false
        		});
        		$("#insinfoonlroute").html('<div class="h6">'+onlcar+' - онлайн маршрут</div><div class="row"><div class="col-6">Время:</div><div class="col-6">'+result.onltime+'</div></div><div class="row"><div class="col-6">Расстояние:</div><div class="col-6">'+onl_poly.measuredDistance()+'</div></div><div class="row"><div class="col-6">Скорость:</div><div class="col-6">'+result.spd+' км/ч</div></div>');
        		$("#infoonlroute").toast('show');
        		onl_poly.addEventListener('click', function(e) {
            		var index = _getClosestPointIndex(e.latlng, coords);
            		var popup = L.popup()
                		.setLatLng(new L.latLng(coords[index]))
                		.setContent(text[index])
                		.openOn(self.map);
				});
				map.panTo(onllatlng);
			}
		},
		"json"
	);
	}, setint);

		jQuery('#onlstop').attr('disabled', false);
		jQuery('#onlstart').attr('disabled', true);
		jQuery('#onlint').attr('disabled', true);
};

function autonlinestop(){
	stopint1 = window.clearInterval(int1);
		jQuery('#onlstop').attr('disabled', true);
		jQuery('#onlstart').attr('disabled', false);
		jQuery('#onlint').attr('disabled', false);
};