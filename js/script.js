/*
$(function() {

alert('На сайте выполняются технические работы. Возможны ошибки или некорректная работа');

});
*/
function init(){
	
arr = [];	/*	Массив для записи номеров маршрутов (используется в grid_script.js)*/

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

var check_adr = jQuery('#check_dom').val();


/*
var localUrl = '../images/osm_tiles/{z}/{x}/{y}.png';
var localUrl = 'http://cis-tile.savushkin.by/osm_tiles/{z}/{x}/{y}.png';
*/
var localUrl = 'http://by-tile.savushkin.by/osm_tiles/{z}/{x}/{y}.png';
var SNGUrl = 'http://cis-tile.savushkin.by/osm_tiles/{z}/{x}/{y}.png';
var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';

var local_blr = L.tileLayer(localUrl, {attribution: osmAttrib, maxZoom: 18}),local_sng = L.tileLayer(SNGUrl, {attribution: osmAttrib, maxZoom: 18}), inet_osm  = L.tileLayer(osmUrl, {attribution: osmAttrib, maxZoom: 18});

if(isMobile.any() || (check_adr == 1)){
	map = L.map('map', {
			center: [53.85, 27.50],
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
			center: [53.85, 27.50],
			zoom: 8,
			doubleClickZoom: false,
/*			renderer : labelTextCollision,*/			
			layers: [local_sng]
	});
	var baseLayers = {
			"Локальная Беларусь OSM": local_blr,
			"Локальная СНГ OSM": local_sng,
			"Интернет карта OSM": inet_osm
	};
}

L.control.scale().addTo(map);
L.Control.measureControl().addTo(map);

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

layerControl = L.control.layers(baseLayers).addTo(map);
var c = new L.Control.Coordinates().addTo(map);

map.on('mousemove', function(e) {
    c.setCoordinates(e);
});

map.on('dblclick', function(e) {
	text = e.latlng.lat.toFixed(6) + ", " + e.latlng.lng.toFixed(6);
	window.prompt ("Чтобы скопировать текст в буфер обмена, нажмите Ctrl+C", text);
})

_getClosestPointIndex = function(lPoint, arrayLPoints) {
	var distanceArray = [];
	for ( var i = 0; i < arrayLPoints.length; i++ ) {
		distanceArray.push( lPoint.distanceTo(arrayLPoints[i]) );
	}
	return distanceArray.indexOf(  Math.min.apply(null, distanceArray) );
}

LeafIcon = L.Icon.extend({
	options: {
		iconUrl: 'images/marker-icon.png',
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

jQuery('#car_id').focus();
}

// Поиск по широте и долготе osm
function Search2()
{
	if ((jQuery('#slat').val() == "")||(jQuery('#slong').val() == "")||(jQuery('#slat').val() == "5x.xx")||(jQuery('#slong').val() == "2x.xx")){
		alert("Введите широту и долготу");
	}
	else{
		if (window.scmarker){
			for(i=0;i<scmarker.length;i++){
				map.removeLayer(scmarker[i]);
			}
		}
		scmarker = new Array();
		var sclatlng = L.latLng(jQuery('#slat').val(),jQuery('#slong').val());
		var scm = new L.marker(sclatlng);
		scmarker.push(scm);
		map.addLayer(scmarker[0]);
		map.setView(sclatlng, 16);
	}
}

// Поиск по адресу osm

function Search_osm()
{
	if (jQuery('#city').val() == ""){
		alert("Введите данные для поиска");
	}
	else{
		var city = jQuery('#city').val();
		var street = jQuery('#street').val();
		var house = jQuery('#house').val();
		jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');
		
		var url = 'namefinder.php';
		jQuery.post(
			url,
			"f_city=" + encodeURIComponent(city) + "&f_street=" + encodeURIComponent(street) + "&f_house=" + encodeURIComponent(house),
			function (result){
				if (result.type !== 'success'){
					jQuery('#progress').html('');
					alert('Ошибка!!! Нет данных от сервера адресов.');
					return(false);
				}
				else{
					if (result.answer !== '') alert (result.answer);
					jQuery('#progress').html('');
					if (window.scmarker){
						for(i=0;i<scmarker.length;i++){
							map.removeLayer(scmarker[i]);
						}
					}
					scmarker = new Array();
					var sclatlng = L.latLng(result.lat,result.lon);
					var scm = new L.marker(sclatlng);
					scmarker.push(scm);
					map.addLayer(scmarker[0]);
					map.setView(sclatlng, 16);
				}
				},
				"json"
		);
	}
}

// Ограничения на ввод символов
function numReal(evt)
{
evt = (evt) ? evt : event;
	var elem = (evt.target) ? evt.target : evt.srcElement
	var code = (evt.charCode) ? evt.charCode : evt.keyCode
	if ((code > 31 && code < 37)||(code > 40 && code < 46)||(code > 46 && code < 48)|| code > 57){
		alert("В данное поле можно вводить числа, и символ \".\" в качестве разделителя дробной части");
		elem.focus()
		return false;
	}
	return true;
}

function numInt(evt)
{
evt = (evt) ? evt : event;
	var elem = (evt.target) ? evt.target : evt.srcElement
	var code = (evt.charCode) ? evt.charCode : evt.keyCode
	if ((code > 31 && code < 37)||(code > 40 && code < 46)||(code > 46 && code < 48)|| code > 57){
		alert("В данное поле можно вводить только целые числа");
		elem.focus()
		return false;
	}
	return true;
}


function Refrcars()
{
	document.mform.action += '?admin=1&addcars=1';
	document.mform.submit();
}

function Backcars()
{
	document.mform.action += '?admin=1';
	document.mform.submit();
}

function sort(n){
	switch (n){
	case "num":{
		if (jQuery('#nsort').val() == '0'){
			jQuery('#nsort').attr('value', 1);
		}else{
			if (jQuery('#nsort').val() == '1'){
				jQuery('#nsort').attr('value', 2);
			}
			else{
				if (jQuery('#nsort').val() == '2') jQuery('#nsort').attr('value', 1);
				}
		}
		jQuery('#lsort').attr('value', 0);
		jQuery('#fsort').attr('value', 0);
		jQuery('#psort').attr('value', 0);
		jQuery('#dsort').attr('value', 0);
		jQuery('#csort').attr('value', 0);
		var stype = jQuery('#nsort').val();
		break;
	}
	case "log":{
		if (jQuery('#lsort').val() == '0'){
			jQuery('#lsort').attr('value', 1);
		}else{
			if (jQuery('#lsort').val() == '1'){
				jQuery('#lsort').attr('value', 2);
			}
			else{
				if (jQuery('#lsort').val() == '2') jQuery('#lsort').attr('value', 1);
				}
		}
		jQuery('#nsort').attr('value', 0);
		jQuery('#fsort').attr('value', 0);
		jQuery('#psort').attr('value', 0);
		jQuery('#dsort').attr('value', 0);
		jQuery('#csort').attr('value', 0);
		var stype = jQuery('#lsort').val();
		break;
	}
	case "fio":{
		if (jQuery('#fsort').val() == '0'){
			jQuery('#fsort').attr('value', 1);
		}else{
			if (jQuery('#fsort').val() == '1'){
				jQuery('#fsort').attr('value', 2);
			}
			else{
				if (jQuery('#fsort').val() == '2') jQuery('#fsort').attr('value', 1);
				}
		}
		jQuery('#nsort').attr('value', 0);
		jQuery('#lsort').attr('value', 0);
		jQuery('#psort').attr('value', 0);
		jQuery('#dsort').attr('value', 0);
		jQuery('#csort').attr('value', 0);
		var stype = jQuery('#fsort').val();
		break;
	}
	case "pos":{
		if (jQuery('#psort').val() == '0'){
			jQuery('#psort').attr('value', 1);
		}else{
			if (jQuery('#psort').val() == '1'){
				jQuery('#psort').attr('value', 2);
			}
			else{
				if (jQuery('#psort').val() == '2') jQuery('#psort').attr('value', 1);
				}
		}
		jQuery('#nsort').attr('value', 0);
		jQuery('#lsort').attr('value', 0);
		jQuery('#fsort').attr('value', 0);
		jQuery('#dsort').attr('value', 0);
		jQuery('#csort').attr('value', 0);
		var stype = jQuery('#psort').val();
		break;
	}
	case "plast":{
		if (jQuery('#dsort').val() == '0'){
			jQuery('#dsort').attr('value', 1);
		}else{
			if (jQuery('#dsort').val() == '1'){
				jQuery('#dsort').attr('value', 2);
			}
			else{
				if (jQuery('#dsort').val() == '2') jQuery('#dsort').attr('value', 1);
				}
		}
		jQuery('#nsort').attr('value', 0);
		jQuery('#lsort').attr('value', 0);
		jQuery('#fsort').attr('value', 0);
		jQuery('#psort').attr('value', 0);
		jQuery('#csort').attr('value', 0);
		var stype = jQuery('#dsort').val();
		break;
	}
	case "code":{
		if (jQuery('#csort').val() == '0'){
			jQuery('#csort').attr('value', 1);
		}else{
			if (jQuery('#csort').val() == '1'){
				jQuery('#csort').attr('value', 2);
			}
			else{
				if (jQuery('#csort').val() == '2') jQuery('#csort').attr('value', 1);
				}
		}
		jQuery('#nsort').attr('value', 0);
		jQuery('#lsort').attr('value', 0);
		jQuery('#fsort').attr('value', 0);
		jQuery('#psort').attr('value', 0);
		jQuery('#dsort').attr('value', 0);
		var stype = jQuery('#csort').val();
		break;
	}
	default:{
		break;
	}
}
	document.mform.action = '?admin=2&sort='+n+'&stype='+stype;
	document.mform.submit();
}

function trsort(n){
	switch (n){
	case "trnum":{
		if (jQuery('#ntrsort').val() == '0'){
			jQuery('#ntrsort').attr('value', 1);
		}else{
			if (jQuery('#ntrsort').val() == '1'){
				jQuery('#ntrsort').attr('value', 2);
			}
			else{
				if (jQuery('#ntrsort').val() == '2') jQuery('#ntrsort').attr('value', 1);
				}
		}
		jQuery('#ttrsort').attr('value', 0);
		jQuery('#ptrsort').attr('value', 0);
		jQuery('#ltrsort').attr('value', 0);
		jQuery('#dtrsort').attr('value', 0);
		var stype = jQuery('#ntrsort').val();
		break;
	}
	case "trsern":{
		if (jQuery('#ttrsort').val() == '0'){
			jQuery('#ttrsort').attr('value', 1);
		}else{
			if (jQuery('#ttrsort').val() == '1'){
				jQuery('#ttrsort').attr('value', 2);
			}
			else{
				if (jQuery('#ttrsort').val() == '2') jQuery('#ttrsort').attr('value', 1);
				}
		}
		jQuery('#ntrsort').attr('value', 0);
		jQuery('#ptrsort').attr('value', 0);
		jQuery('#ltrsort').attr('value', 0);
		jQuery('#dtrsort').attr('value', 0);
		var stype = jQuery('#ttrsort').val();
		break;
	}
	case "trlog":{
		if (jQuery('#ltrsort').val() == '0'){
			jQuery('#ltrsort').attr('value', 1);
		}else{
			if (jQuery('#ltrsort').val() == '1'){
				jQuery('#ltrsort').attr('value', 2);
			}
			else{
				if (jQuery('#ltrsort').val() == '2') jQuery('#ltrsort').attr('value', 1);
				}
		}
		jQuery('#ptrsort').attr('value', 0);
		jQuery('#ntrsort').attr('value', 0);
		jQuery('#ttrsort').attr('value', 0);
		jQuery('#dtrsort').attr('value', 0);
		var stype = jQuery('#ltrsort').val();
		break;
	}
	case "trdate":{
		if (jQuery('#dtrsort').val() == '0'){
			jQuery('#dtrsort').attr('value', 1);
		}else{
			if (jQuery('#dtrsort').val() == '1'){
				jQuery('#dtrsort').attr('value', 2);
			}
			else{
				if (jQuery('#dtrsort').val() == '2') jQuery('#dtrsort').attr('value', 1);
				}
		}
		jQuery('#ntrsort').attr('value', 0);
		jQuery('#ttrsort').attr('value', 0);
		jQuery('#ltrsort').attr('value', 0);
		jQuery('#ptrsort').attr('value', 0);
		var stype = jQuery('#dtrsort').val();
		break;
	}
	case "trperev":{
		if (jQuery('#ptrsort').val() == '0'){
			jQuery('#ptrsort').attr('value', 1);
		}else{
			if (jQuery('#ptrsort').val() == '1'){
				jQuery('#ptrsort').attr('value', 2);
			}
			else{
				if (jQuery('#ptrsort').val() == '2') jQuery('#ptrsort').attr('value', 1);
				}
		}
		jQuery('#dtrsort').attr('value', 0);
		jQuery('#ntrsort').attr('value', 0);
		jQuery('#ttrsort').attr('value', 0);
		jQuery('#ltrsort').attr('value', 0);
		var stype = jQuery('#ptrsort').val();
		break;
	}
	default:{
		break;
	}
}
	document.mform.action = 'index.php?treker=1&sort='+n+'&trtype='+stype;
	document.mform.submit();
}

function RegTr()
{
	document.mform.action = 'index.php?treker=1&add=1';
	document.mform.submit();
}

function Remall(r)
{
	if (confirm("Вы подтверждаете удаление?")){
		document.mform.action = 'index.php?admin='+r+'&del=1';
		document.mform.submit();
	}else{
		return false;
	}
}

function Back(backto)
{
	document.mform.action = 'index.php?admin='+backto;
	document.mform.submit();
}

function Backtoadd(backto){
	document.mform.action = 'index.php?admin='+backto+'&add=1';
	document.mform.submit();
}

function Backtoaddtr(){
	document.mform.action = 'index.php?treker=1&add=1';
	document.mform.submit();
}

function Backtr()
{
	document.mform.action = 'index.php?treker=1';
	document.mform.submit();
}

function Addall(backto)
{
	document.mform.action = 'index.php?admin='+backto+'&add=1';
	document.mform.submit();
}

function Addperproc()
{
/*
	if (jQuery('#npcode').val() == ""){
		alert("Заполните поле \"Код перевозчика\"");
		jQuery('#npcode').focus();
	}
	else{
*/
		if (jQuery('#nperev').val() == ""){
			alert("Заполните поле \"Перевозчик\"");
			jQuery('#nperev').focus();
		}
		else{
/*
			var re = /^[0-9]*$/;
			if (!re.test(jQuery('#npcode').val())){
				alert ('В поле \"Код перевозчика\" можно вводить только цифры');
				jQuery('#npcode').focus();
			}
			else{
*/
				document.mform.action = 'index.php?admin=1&add=1&addperproc=1';
				document.mform.submit();
/*
			}
*/
	}
/*
	}
*/
}

function Addcarproc()
{
	if (jQuery('#n_anum').val() == ""){
		alert("Заполните поле \"Номер машины\"");
		jQuery('#n_anum').focus();
	}
	else{
		if (jQuery('#n_carlctn').val() == ""){
			alert("Заполните поле \"Местоположение\"");
			jQuery('#n_carlctn').focus();
		}
		else{
			if (jQuery('#perev').val() == ""){
				alert("Заполните поле \"Перевозчик\"");
				jQuery('#perev').focus();
			}
			else{
				var re = /^[a-zA-Z]+[0-9]+$/;
				if (!re.test(jQuery('#n_anum').val())){
					alert ('В поле \"Номер машины\" номер следует вводить только латинскими буквами и цифрами без разделителей. Сначала буквы, потом цифры.');
					jQuery('#n_anum').focus();
				}
				else{
					document.mform.action = 'index.php?admin=3&add=1&addcarproc=1';
					document.mform.submit();
				}
			}
		}
	}
}

function Addproc()
{
	if ((document.getElementById('nuserid').value == "")||(document.getElementById('npassid').value == "")){
		alert("Проверьте заполненность полей \"Логин\", \"Пароль\"");
	}
	else{
		document.mform.action = 'index.php?admin=2&add=1&addproc=1';
		document.mform.submit();
	}
}

function Editperproc()
{
	if (jQuery('#edpcode').val() == ""){
		alert("Заполните поле \"Код перевозчика\"");
		jQuery('#edpcode').focus();
	}
	else{
		if (jQuery('#edpname').val() == ""){
			alert("Заполните поле \"Перевозчик\"");
			jQuery('#edpname').focus();
		}
		else{
			var re = /^[0-9]*$/;
			if (!re.test(jQuery('#edpcode').val())){
				alert ('В поле \"Код перевозчика\" можно вводить только цифры');
				jQuery('#edpcode').focus();
			}
			else{
				document.mform.action = 'index.php?admin=1&edit=1&editperproc=1';
				document.mform.submit();
			}
	}
	}
}

function Editproc()
{
	if (jQuery('#nuserid').val() == ""){
		alert("Введите Логин");
		jQuery('#nuserid').focus();
	}
	else{
		if (jQuery("#chpass").prop("checked")&&jQuery('#chpassid').val() == ""){
			alert("Введите Пароль");
			jQuery('#chpassid').focus();
		}
		else{
			document.mform.action = 'index.php?admin=2&edit=1&editproc=1';
			document.mform.submit();
		}
	}
}

function Addtrproc()
{
	if ((jQuery('#car_id').val() == "")||(jQuery('#treker_id').val() == "")){
		alert("Проверьте заполненность полей \"Номер машины\", \"Номер трекера\"");
	}
	else{
		document.mform.action = 'index.php?treker=1&add=1&addtrproc=1';
		document.mform.submit();
	}
}

function Editall(ed)
{
	document.mform.action = 'index.php?admin='+ed+'&edit=1';
	document.mform.submit();
}

function RemTr()
{
	if (confirm("Вы подтверждаете снятие трекера?")){
		document.mform.action = 'index.php?treker=1&del=1';
		document.mform.submit();
	}else{
		return false;
	}
}


// Показать/скрыть управление

function showhide(cel, cel2){
if (jQuery('#'+cel).css('visibility') == 'visible'){
		jQuery('#'+cel).css('visibility', 'hidden');	
		jQuery('#'+cel2).css('visibility', 'visible');		
	}
else{
		jQuery('#'+cel2).css('visibility', 'hidden');
		jQuery('#'+cel).css('visibility', 'visible');
}
	return false;
};

function showhide2(cel){
	jQuery('#'+cel).css('display', 'none');
}

function delinfo(){
	jQuery('#result').html('');
}


function newcar(){
	if (jQuery('#car_id').val() == 'other'){
		jQuery('#addncar').html('Номер машины вводить в формате АА1111 без пробелов, тире и т.п.<div><input class=\"otst3\" type=\"text\" name=\"inpnewcar\" id=\"inpnewcar\" maxlength=\"10\"/></div>');
	}else{
		jQuery('#addncar').html('');
	}
}

function f_orders(rid){
	jQuery('#orders').html('');
	var url = 'get_orders.php';
	jQuery.get(
		url,
		"rid=" + rid,
		function (result){
			if (result.type == 'error'){
				alert(result.msg);
				return false;
			}
			else {
				jQuery('#orders').css('display','block');
				var x = jQuery('#orders').html();
				x = '<div class=\"zot\">Заказы</div><div id="closecontrol" class="rlabel" onclick="showhide2(\'orders\')"></div><table id="tponline" width="100%" cellpadding="1" cellspacing="0"><tr class="tptr"><td class="tdol1">Отправитель (склад)</td><td class="tdol1">Получатель (склад/магазин)</td><td class="tdol1">Дата погрузки/доставки (план)</td><td class="tdol1">Мест</td><td class="tdol1">Вес</td><td class="tdol1">Статус</td><td class="tdol1">Документы</td></tr>';
				jQuery(result.orders).each(function(){
				x += '<tr class="tptr2"><td class="tdol1">'+jQuery(this).attr('postav')+' ('+jQuery(this).attr('psklad')+')</td><td class="tdol1">'+jQuery(this).attr('zakaz')+' ('+jQuery(this).attr('zsklad')+')</td><td class="tdol1">'+jQuery(this).attr('dt')+'</td><td class="tdol1">'+jQuery(this).attr('kolm')+'</td><td class="tdol1">'+jQuery(this).attr('vb')+'</td><td class="tdol1">'+jQuery(this).attr('ostat')+'</td><td class="tdol1"><div class=\"imit_a\" onclick=\"f_docs('+rid+','+jQuery(this).attr('oid')+')\">Документы ('+jQuery(this).attr('coldoc')+')</div></td></tr>';
				});
				x += '</table>';
				jQuery('#orders').html(x);
			}
		},
		"json"
	);
}

function f_upload(){
	if (jQuery('#upldoc').val() == ''){
		alert ('Выберите файл для загрузки');
		return false;
	}else{
		jQuery('#form_upl').submit();
		jQuery('#upload').hide();
		jQuery('#res').html("Идет загрузка файла");
	}
}

function handleResponse(mes){
	jQuery('#upload').show();
	if (mes.errors != null){
		alert ("Возникли ошибки во время загрузки файла: " + mes.errors);
	}
	else {
		alert ("Файл " + mes.name + " загружен"); 
	}    
}

function f_upload2(){
	if (jQuery('#upldoc2').val() == ''){
		alert ('Выберите файл для загрузки');
		return false;
	}else{
		jQuery('#form_upltrack').submit();
		jQuery('#upltrack').hide();
		jQuery('#res2').html("Идет загрузка файла");
	}
}

function handleResponse2(mes){
	jQuery('#upltrack').show();
	if (mes.errors != null){
		alert ("Возникли ошибки во время загрузки файла: " + mes.errors);
		return false;
	}
	else {
/*
		alert ("Файл " + mes.name + " загружен");    
*/
		jQuery('#res2').html('');
	}    
}

function f_docs(rid, oid){
	jQuery('#docs').html('');
	var url = 'get_docs.php';
	jQuery.get(
		url,
		"rid=" + rid + "&oid=" + oid, 
		function (result){
			if (result.type == 'error'){
				alert(result.msg);
				return false;
			}
			else {
				jQuery('#docs').css('display','block');
				var x = jQuery('#docs').html();
				x = '<div class=\"zot\">Документы</div><div id="closecontrol" class="rlabel" onclick="showhide2(\'docs\')"></div><table id="tponline" width="100%" cellpadding="1" cellspacing="0"><tr class="tptr"><td class="tdol1">Тип документа</td><td class="tdol1">Формат документа</td><td class="tdol1">Скачать документ</td><td class="tdol1">Хозяин документа</td></tr>';
				jQuery(result.docs).each(function(){
				x += '<tr class="tptr2"><td class="tdol1">'+jQuery(this).attr('type')+'</td><td class="tdol1">'+jQuery(this).attr('format')+'</td><td class="tdol1"><a href="savefile.php?doc='+jQuery(this).attr('id')+'&user='+jQuery(this).attr('uid')+'&ext='+jQuery(this).attr('format')+'" target="_blank">'+jQuery(this).attr('doc')+'</a></td><td class="tdol1">'+jQuery(this).attr('uid')+'</td></tr>';
				});
				x += '</table>';
				x += '<div id="fupldocs"><form id="form_upl" enctype="multipart/form-data" action="?route=1" method="post" target="hiddenframe"><input type="hidden" name="MAX_FILE_SIZE" value="2000000"/><input type="file" name="upldoc" id="upldoc" class="ifile"><input name="upload" id="upload" type="button" class="linp" value="Загрузить документ" onclick="f_upload()"><input type="hidden" name="rid" value="'+rid+'"><input type="hidden" name="oid" value="'+oid+'"></form></div><div id="res"></div><iframe id="hiddenframe" name="hiddenframe" style="width:0px; height:0px; border:0px"></iframe>';
				jQuery('#docs').html(x);
			}
		},
		"json"
	);
}

function RoadsAddr(evt,tp){	// Функция контекстного поиска по адресу
	evt = (evt) ? evt : event;
	var elem = (evt.target) ? evt.target : evt.srcElement
	var code = (evt.charCode) ? evt.charCode : evt.keyCode
	if (code == 13){
	su_name = jQuery('#u_id').val();
	var addr = jQuery('#searchadrroad'+tp).val();
	if (addr == ""){
		alert("Введите адрес или часть адреса в поле для поиска");
		return false;
	}
	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');

	var f_gwx = 'Lctn';

	var url = 'get_latlon.php';

	jQuery.post(
		url,
		"su_name=" + su_name + "&f_gwx=" + f_gwx + "&addr=" + encodeURIComponent(addr),
		function (result){
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert (result.res);
				return(false);
			}
			else {
				jQuery('#progress').html('');
				jQuery('#rasst').html('');
				jQuery('#arraddr'+tp).html('');
				if (tp == 1){jQuery('#searchadrroad2').focus()}else{jQuery('#rasstroadbtn').focus()}
				jQuery(result.address).each(function(){
					var objaddress = jQuery(this).attr('addr');
					var objlat = jQuery(this).attr('lat');
					var objlon = jQuery(this).attr('lon');
					jQuery('#arraddr'+tp).append('<option value="'+objlon+','+objlat+','+objaddress+'">'+objaddress+'</option>');
				});
				var skoord = jQuery('#arraddr'+tp).val();
				var arkord = skoord.split(/,/);
				jQuery('#searchadrroad'+tp).attr('value',arkord[2]);
				if (result.count !== 1){
					jQuery('#arraddr'+tp).css('display','block');
				}else{
					jQuery('#arraddr'+tp).css('display','none');
				}
				if (window.scmarker){
					for(i=0;i<scmarker.length;i++){
						map.removeLayer(scmarker[i]);
					}
				}
				scmarker = new Array();
				var sclatlng = L.latLng(arkord[1],arkord[0]);
				var scm = new L.marker(sclatlng).bindPopup('<b>'+addr+'</b>');
				scmarker.push(scm);
				map.addLayer(scmarker[0]);
				map.setView(sclatlng, 12);
			}
		},
		"json"
	);
	}else{
		return false;
	}
};

function newobj(tp){
	var skoord = jQuery('#arraddr'+tp).val();
	var arkord = skoord.split(/,/);
	jQuery('#searchadrroad'+tp).attr('value',arkord[2]);
	var addr = jQuery('#searchadrroad'+tp).val();
	if (window.scmarker){
		for(i=0;i<scmarker.length;i++){
			map.removeLayer(scmarker[i]);
		}
	}
	scmarker = new Array();
	var sclatlng = L.latLng(arkord[1],arkord[0]);
	var scm = new L.marker(sclatlng).bindPopup('<b>'+addr+'</b>');
	scmarker.push(scm);
	map.addLayer(scmarker[0]);
	map.setView(sclatlng, 12);
	jQuery('#searchadrroad'+tp).focus();
}

function rasst_clm(){
	var counter = jQuery('#trcounter').val();
	var su_name = jQuery('#u_id').val();
	var from = jQuery('#arraddr1').val();
	var to = jQuery('#arraddr2').val();
	var trall = '';
	if (from == null||to == null){
		alert ('Выберите начальную и конечную точку');
		jQuery('#searchadrroad1').focus();
		return false;
	}else{
		if (counter > 3){
			for (ni = 3; ni < counter; ni++){
				var transit = jQuery('#arraddr'+ni).val();
				if (transit == null){
					alert ('Выберите транзитную точку');
					jQuery('#searchadrroad'+ni).focus();
					return false;
				}else{
					var artrpoint = transit.split(/,/);					
					var trpointlat = artrpoint[1];
					var trpointlon = artrpoint[0];
					if (ni == 3){
						trall += trpointlat+','+trpointlon;
					}else{
						trall += ','+trpointlat+','+trpointlon;
					}
				}
			}
		}else{
			trall = 0;
		}
		var arfrom = from.split(/,/);
		var arto = to.split(/,/);
		var fromlat = arfrom[1];
		var fromlon = arfrom[0];
		var tolat = arto[1];
		var tolon = arto[0];
//		alert(fromlat+', '+fromlon+', '+tolat+', '+tolon);
		var url = 'get_road.php';
		jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');
		jQuery.get(
		url,
		"su_name=" + su_name + "&fromlat=" + fromlat + "&fromlon=" + fromlon + "&tolat=" + tolat + "&tolon=" + tolon + "&trpoints=" + trall,
		function (result){
			if (!result){
				jQuery('#progress').html('');
				alert ('Ошибка связи');
				return(false);
			}
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert ('Ошибка связи');
				return(false);
			}
			else {
				if (window.scmarker){
					for(i=0;i<scmarker.length;i++){
						map.removeLayer(scmarker[i]);
					}
				}
				if (window.r_poly){
					map.removeLayer(r_poly);
				}
				jQuery('#progress').html('');
				var distkm = result.dist/1000;
				jQuery('#rasst').html('Расстояние: '+distkm+' км');

				var roadcoords = new Array();
				roadcoords.length = 0;
				jQuery(result.t_road).each(function() {
					var latlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					roadcoords.push(latlng);
				});
				r_poly = L.polyline(roadcoords,{color: 'red', weight: 4, opacity: 0.7}).addTo(map);
				map.fitBounds(r_poly.getBounds());
			}
		},
		"json"
	);
	}
}

function handpoint(cel,tp){
	if (jQuery('#phand'+cel).prop("checked")){
		var numtr = cel - 2;
		if (tp == 1) var mess = 'Начальная точка';
		if (tp == 2) var mess = 'Конечная точка';
		if (tp == 3) var mess = 'Транзитная точка '+ numtr;
		map.on('click', function(e) {
		var	pointlat = e.latlng.lat.toFixed(6);
		var	pointlon = e.latlng.lng.toFixed(6);

		jQuery('#searchadrroad'+cel).attr('value',pointlon+','+pointlat);
		jQuery('#arraddr'+cel).css('display','none');
		jQuery('#arraddr'+cel).html('');
		jQuery('#arraddr'+cel).append('<option value="'+pointlon+','+pointlat+'">'+pointlon+','+pointlat+'</option>');
		if (cel == 1){jQuery('#searchadrroad2').focus()}else{jQuery('#rasstroadbtn').focus()}
		if (window.scmarker){
			for(i=0;i<scmarker.length;i++){
				map.removeLayer(scmarker[i]);
			}
		}
		scmarker = new Array();
		var sclatlng = L.latLng(pointlat,pointlon);
		var scm = new L.marker(sclatlng).bindPopup('<b>'+mess+'</b>');
		scmarker.push(scm);
		map.addLayer(scmarker[0]);
		jQuery('#phand'+cel).removeAttr('checked');
		map.off("click");
		});
	}else{
		map.off("click");		
	}
}

function transit(){
	var ci = jQuery('#trcounter').val();
	var ntr = ci - 2;
	if (ci == 13) return false;
	var tr_cont = jQuery('#instransit').html();
	tr_cont += '<div id="trans'+ci+'"><div class="form-group"><input type="text" name="adrsearch'+ci+'" id="searchadrroad'+ci+'" class="form-control" placeholder="Транзитная точка '+ntr+'" value="" maxlength="30" onkeydown="RoadsAddr(event,'+ci+')" onclick="focuson(this)"/><div class="form-check form-group"><input type="checkbox" class="form-check-input rlabel" id="phand'+ci+'" onchange="handpoint('+ci+',3)"/><label for="phand'+ci+'" class="form-check-label rlabel">Указать на карте</label></div></div><div class="form-group"><select class="form-control d-none" id="arraddr'+ci+'" onchange="newobj('+ci+')"></select></div>';
	jQuery('#instransit').html(tr_cont);
	jQuery('#searchadrroad'+ci).focus();
	ci++;
	jQuery('#trcounter').attr('value',ci);
}

function mtransit(){
	var ci = jQuery('#trcounter').val();
	if (ci == 3) return false;
	ci--;
	var tr_cont = jQuery('#trans'+ci).html('');
	jQuery('#trans'+ci).remove();
	var foctr = ci - 1;
	jQuery('#searchadrroad'+foctr).focus();
	jQuery('#trcounter').attr('value',ci);
}

function search_osm_ent(evt){
	evt = (evt) ? evt : event;
	var elem = (evt.target) ? evt.target : evt.srcElement
	var code = (evt.charCode) ? evt.charCode : evt.keyCode
	if (code == 13){
		return Search_osm();
	}
}

function focuson(obj){
	obj.focus();
}

function hidesave(){
	if (jQuery('#savetr'))jQuery('#savetr').html('');
}

function sendtr(){
	jQuery('#f1').attr('action','savetrack.php');
	jQuery('#f1').attr('target','_blank');
	jQuery('#f1').submit();
}


function rmarker(){
	if (window.repmarker){
		for(i=0;i<repmarker.length;i++){
			map.removeLayer(repmarker[i]);
		}
	}
	repmarker = new Array();
	var replatlng = L.latLng(jQuery('#replat').val(),jQuery('#replon').val());
	var rcm = new L.marker(replatlng);
	repmarker.push(rcm);
	map.addLayer(repmarker[0]);
	map.setView(replatlng, 16);
}

function showgroup(){
	if (window.grmarker){
		for(i = 0; i < grmarker.length; i++){
			map.removeLayer(grmarker[i]);
		}
		delete grmarker;
	}
	var u_name = $('#u_id').val();
	var u_owner = $('#u_own').val();
	u_own
	if ($('#mash').is(':checked')){
		var mash = 1;
	}else{
		var mash = 0;
	}
	if ($('#mvoz').is(':checked')){
		var mvoz = 1;
	}else{
		var mvoz = 0;
	}
	if ($("#sav").length){
		if ($('#sav').is(':checked')){
			var sav = 1;
		}else{
			var sav = 0;	
		}
	}else{
		var sav = 0;
	}
	var lctn = "";
	$('.gr-lctn').each(function(){
    	if($(this).is(':checked')) lctn += $(this).val() + ",";
	});
	if (lctn.length > 0) lctn = lctn.slice(0,-1);
	if (mash == 0 && mvoz == 0){
		alert ("Необходимо выбрать хотя бы одну группу автомобилей");
		return false;		
	}
	if (lctn == ""){
		alert ("Необходимо выбрать хотя бы один город для группы автомобилей");
		return false;		
	}
	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');
	var url = 'get_grauto.php';
	jQuery.post(
		url,
		{
			'u_name': u_name,
			'u_owner': u_owner,
			'mash': mash,
			'mvoz': mvoz,
			'sav': sav,
			'lctn': lctn
		},
		function (result){
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert('Ошибка!');
				return(false);
			}
			else {
				grmarker = new Array();
				var grcoords = new Array();
				var mcolor = '';
				var i = 0;
				jQuery(result.gr_cars).each(function(){
					var grlatlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					var type = jQuery(this).attr('type');
					if (type == 0){
						mcolor = red;
					}else{
						mcolor = yellow;
					}
					var grm = new L.marker(grlatlng,{icon: mcolor,riseOnHover: true}).bindPopup(jQuery(this).attr('text'));
					grmarker.push(grm);
					grcoords.push(grlatlng);
					map.addLayer(grmarker[i]);
					i++;
				});
				var bounds = new L.LatLngBounds(grcoords);
				map.fitBounds(bounds);
				jQuery('#progress').html('');
			}
		},
		"json"
	);
}


function showferms(){
	if (window.fermsmarker){
		for(i=0;i<fermsmarker.length;i++){
			map.removeLayer(fermsmarker[i]);
		}
		layerControl.removeLayer(ferms_LayerGroup);
		delete fermsmarker;
		jQuery('#ajferms').attr('value','Показать фермы');
		return false;
	}
	var su_name = jQuery('#u_id').val();
	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');
	var url = 'get_ferms.php';
	jQuery.get(
		url,
		"su_name=" + su_name,
		function (result){
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert('Ошибка!');
				return(false);
			}
			else {
				ferms_LayerGroup = L.layerGroup();
				fermsmarker = new Array();
				fermscoords = new Array();
				var i = 0;
				jQuery(result.ferms).each(function(){
					var fermslatlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					var fermsm = new L.marker(fermslatlng,{icon: red,riseOnHover: true}).bindPopup(jQuery(this).attr('text'));
					fermsmarker.push(fermsm);
					fermscoords.push(fermslatlng);
					map.addLayer(fermsmarker[i]);
					ferms_LayerGroup.addLayer(fermsmarker[i]);
					i++;
				});
				ferms_LayerGroup.addTo(map); layerControl.addOverlay(ferms_LayerGroup, 'Фермы');
				var bounds = new L.LatLngBounds(fermscoords);
				map.fitBounds(bounds);
				jQuery('#ajferms').attr('value','Скрыть фермы');
				jQuery('#progress').html('');
			}
		},
		"json"
	);
}

function chcolor(col){
	jQuery('#colortrack').attr('value',col);
}

function izmer(){
	if (jQuery('#lineToggle').val() == '1'){
		jQuery('#lineToggle').attr('value','0');
		jQuery('#izmer').css('border','none');
	}else{
		jQuery('#izmer').css('border','1px solid black');
		jQuery('#lineToggle').attr('value','1');
	}
toggleControl();
}

function sferm(){
	var fkoord = jQuery('#ferm_id').immybox('getValue')+'';
	if (fkoord == ''){
		alert ('Выберите ферму');
	}else{
	var fermsplit = fkoord.split(',');
	
	
	if (window.scmarker){
		for(i=0;i<scmarker.length;i++){
			map.removeLayer(scmarker[i]);
		}
	}
	scmarker = new Array();
	var sclatlng = L.latLng(fermsplit[0],fermsplit[1]);
	var scm = new L.marker(sclatlng);
	scmarker.push(scm);
	map.addLayer(scmarker[0]);
	map.setView(sclatlng, 14);
	
	if (jQuery('#searchadrroad1').val() == ''){
		jQuery('#searchadrroad1').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr1').css('display','none');
		jQuery('#arraddr1').html('');
		jQuery('#arraddr1').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad3') && jQuery('#searchadrroad3').val() == ''){
		jQuery('#searchadrroad3').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr3').css('display','none');
		jQuery('#arraddr3').html('');
		jQuery('#arraddr3').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad4') && jQuery('#searchadrroad4').val() == ''){
		jQuery('#searchadrroad4').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr4').css('display','none');
		jQuery('#arraddr4').html('');
		jQuery('#arraddr4').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad5') && jQuery('#searchadrroad5').val() == ''){
		jQuery('#searchadrroad5').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr5').css('display','none');
		jQuery('#arraddr5').html('');
		jQuery('#arraddr5').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad6') && jQuery('#searchadrroad6').val() == ''){
		jQuery('#searchadrroad6').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr6').css('display','none');
		jQuery('#arraddr6').html('');
		jQuery('#arraddr6').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad7') && jQuery('#searchadrroad7').val() == ''){
		jQuery('#searchadrroad7').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr7').css('display','none');
		jQuery('#arraddr7').html('');
		jQuery('#arraddr7').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad8') && jQuery('#searchadrroad8').val() == ''){
		jQuery('#searchadrroad8').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr8').css('display','none');
		jQuery('#arraddr8').html('');
		jQuery('#arraddr8').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad9') && jQuery('#searchadrroad9').val() == ''){
		jQuery('#searchadrroad9').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr9').css('display','none');
		jQuery('#arraddr9').html('');
		jQuery('#arraddr9').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad10') && jQuery('#searchadrroad10').val() == ''){
		jQuery('#searchadrroad10').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr10').css('display','none');
		jQuery('#arraddr10').html('');
		jQuery('#arraddr10').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad11') && jQuery('#searchadrroad11').val() == ''){
		jQuery('#searchadrroad11').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr11').css('display','none');
		jQuery('#arraddr11').html('');
		jQuery('#arraddr11').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad12') && jQuery('#searchadrroad12').val() == ''){
		jQuery('#searchadrroad12').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr12').css('display','none');
		jQuery('#arraddr12').html('');
		jQuery('#arraddr12').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	if (jQuery('#searchadrroad2').val() == ''){
		jQuery('#searchadrroad2').attr("value",fermsplit[1]+','+fermsplit[0]);
		jQuery('#arraddr2').css('display','none');
		jQuery('#arraddr2').html('');
		jQuery('#arraddr2').append('<option value="'+fermsplit[1]+','+fermsplit[0]+'">'+fermsplit[1]+','+fermsplit[0]+'</option>');
		return false;
	}
	}
}


function add_newp(){
	var n_perev = jQuery('#add_perev').val();
	
	if (n_perev == ''){
		alert ('Введите название перевозчика');
		jQuery('#add_perev').focus();
		return false;
	}
	var url = 'add_perev.php';
	jQuery.post(
		url,
		"perev=" + encodeURIComponent(n_perev), 
		function (result){
			if (result.type == 'error'){
				alert(result.msg);
				jQuery('#add_perev').select();
				jQuery('#add_perev').focus();
				return false;
			}
			else {
				alert(result.msg);
/*				location.reload();*/
				window.location = "index.php?treker=1&add=1"
			}
		},
		"json"
	);	
}

function imit_click(evt){
	evt = (evt) ? evt : event;
	var elem = (evt.target) ? evt.target : evt.srcElement
	var code = (evt.charCode) ? evt.charCode : evt.keyCode
	if (code == 13){
		jQuery('#btn_add_perev').click();
	}	
}

function add_newa(){
	var a_number = jQuery('#add_auto').val();
	var a_lctn = jQuery('#n_carlctn').val();
	var a_perev = jQuery('#perev2').immybox('getValue');
	var a_carname = jQuery('#n_aname').val();
	var a_driver = jQuery('#n_driver').val();
	var a_tel = jQuery('#n_tel').val();
	var a_hol = jQuery('#n_holod').val();
		
	if(!(/^[a-zA-Z0-9]+$/.test(a_number))){
		alert('Номер автомобиля должен состоять только из комбинации латинских букв и арабских цифр!');
		jQuery('#add_auto').select();
		jQuery('#add_auto').focus();
		return false;
	};
	if (a_lctn == ''){
		alert ('Выберите местоположение');
		return false;
	}
	if (a_perev == ''){
		alert ('Выберите перевозчика');
		return false;
	}
	var url = 'add_cars.php';
	jQuery.get(
		url,
		"a_number=" + a_number + "&a_lctn=" + a_lctn + "&a_perev=" + a_perev + "&a_carname=" + encodeURIComponent(a_carname) + "&a_driver=" + encodeURIComponent(a_driver) + "&a_tel=" + encodeURIComponent(a_tel) + "&a_hol=" + encodeURIComponent(a_hol), 
		function (result){
			if (result.type == 'error'){
				alert(result.msg);
				jQuery('#add_auto').select();
				jQuery('#add_auto').focus();
				return false;
			}
			else {
				alert(result.msg);
				location.reload();
			}
		},
		"json"
	);	
}

function shpass(){
	if (jQuery("#chpass").prop("checked")){
		jQuery("#chpassid").css("visibility","visible");
		jQuery("#chpassid").focus();
	}else{
		jQuery("#chpassid").css("visibility","hidden");
	}
}

function sh_smena(ntr){
	if (ntr == 0){
		jQuery("#smena").css("display","none");
	}else{
		jQuery("#smena").css("display","block");
	}

}

function OSCarOnMap(){
	var su_name = jQuery('#u_id').val();
	var gidcar = jQuery('#car_id').val();

	if (gidcar == ''){
		alert('Не выбран автомобиль');
		jQuery('#car_id').focus();
		return(false);
	}
	jQuery('#progress').html('');
	
	var url = 'get_lastcars.php';
	jQuery.get(
		url,
		"su_name=" + su_name + "&car=" + gidcar,
		function (result) {
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert('Ошибка! Данной машины нет в базе данных!');
				return(false);
			}
			else {
				if (window.lcmarker){
					for(i=0;i<lcmarker.length;i++){
						map.removeLayer(lcmarker[i]);
					}
				}
				lcmarker = new Array();
				var lclatlng = L.latLng(result.lat,result.lon);
				var lcm = new L.marker(lclatlng).bindPopup(result.text);
				lcmarker.push(lcm);
				map.addLayer(lcmarker[0]);
				map.setView(lclatlng, 14);
				jQuery('#progress').html('');
			}
		},
		"json"
	);
};

function AllCarsOnMap(){
	var su_name = jQuery('#u_id').val();
/*
	if (jQuery('#old').attr('checked')){
		var fold = 1;
	}
	else{
		var fold = 0;
	}
*/
	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');
	var url = 'get_cars.php';

	jQuery.get(
		url,
/*		"su_name=" + su_name + "&flagrep=0&flagold=" + fold,*/
		"su_name=" + su_name + "&flagrep=0",
		function (result) {
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert('Ошибка!');
				return(false);
			}
			else{
				if (window.carsmarker){
					for(i=0;i<carsmarker.length;i++){
						map.removeLayer(carsmarker[i]);
					}
					layerControl.removeLayer(cars_LayerGroup);
					delete carsmarker;
				}
				cars_LayerGroup = L.layerGroup();
				carsmarker = new Array();
				carscoords = new Array();
				var i = 0;
				jQuery(result.cars).each(function(){
					var carslatlng = L.latLng(jQuery(this).attr('lat'), jQuery(this).attr('lon'));
					var carsm = new L.marker(carslatlng,{riseOnHover: true}).bindPopup(jQuery(this).attr('text'));
					carsmarker.push(carsm);
					carscoords.push(carslatlng);
					map.addLayer(carsmarker[i]);
					cars_LayerGroup.addLayer(carsmarker[i]);
					i++;
				});
				cars_LayerGroup.addTo(map); layerControl.addOverlay(cars_LayerGroup, 'Все машины');
				var bounds = new L.LatLngBounds(carscoords);
				map.fitBounds(bounds);
				jQuery('#progress').html('');
			}
		},
		"json"
	);
};

function frep_all(){	// Функция для всех отчетов

	var su_name = jQuery('#u_id').val();
	var su_type = jQuery('#u_tp').val();
	var car = jQuery('#car_id').val();
	var sdate = jQuery('#sour').val();
	var podate = jQuery('#dour').val();
	var pref = ''; 
	if (jQuery('#all_day').prop('checked') == true){
		var alld = 1;
	}
	else{
		var alld = 0;
	}
		var dt1 = sdate.substr(6, 4) + '-' + sdate.substr(3, 2) + '-' + sdate.substr(0, 2) + 'T' + sdate.substr(11, 5) + ':00.000+03:00';
		var dt2 = podate.substr(6,4) + '-' + podate.substr(3, 2) + '-' + podate.substr(0, 2) + 'T' + podate.substr(11, 5) + ':00.000+03:00';
		
		if (dt1 > dt2){
			alert ('Проверьте правильность введённого интервала дат');
			return(false);
		}
	if (car == ''){
		alert('Не выбрана машина');
		return(false);
	}
	const reports = document.querySelectorAll('input[name="report"]');
	for (const r of reports) {
	  if (r.checked) {
		repType = r.value
	  }
	}
	if (repType == 1){
		var f_gwx = 'intrep';
		var rep_pref = 'RepInterval';
	}
	if (repType == 2){
		var f_gwx = 'repstop';
		var rep_pref = 'RepStop';
	}
	if (repType == 3){
		var f_gwx = 'repcmp';
		var rep_pref = 'RepCmp';
	}
	if (repType == 4){
		var f_gwx = 'reptmpr';
		var rep_pref = 'RepTmpr';
	}
	if (repType == 6){
		var f_gwx = 'repfuel';
		var rep_pref = 'RepFuel';
	}
	if (repType == 5){
		var f_gwx = 'repsteptmpr';
		var rep_pref = 'RepStepTmpr';
	}
	var reptype = 'html';
	var pref = '_osm'; 

	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');

	var url = 'get_gwx.php';

	jQuery.get(
		url,
		"su_name=" + su_name + "&su_type=" + su_type + "&car=" + car + "&sdt=" + sdate + "&podt=" + podate + "&reptype=" + reptype + "&f_gwx=" + f_gwx + "&allday=" + alld,
		function (result){
			jQuery('#progress').html('');
			if (result.type == 'error'){
				alert (result.res);
				return(false);
			}
			else{
				if (f_gwx  == 'reptmpr'){
					if (result.ALARMS.length > 0){
						var alarmsList = '<table class="table table-striped"><thead><tr><th scope="col">Начало</th><th scope="col">Конец</th><th scope="col">T в начале, &deg;C</th><th scope="col">T в конце, &deg;C</th><th scope="col">Mин t, &deg;C</th><th scope="col">Mакс t, &deg;C</th><th scope="col">Координаты</th></tr></thead><tbody>';
						result.ALARMS.forEach(item => {
							alarmsList += '<tr><td>' + item.DTBEG + '</td><td>' + item.DTEND + '</td><td>' + item.TMPRBEG + '</td><td>' + item.TMPREND + '</td><td>' + item.TMPRMIN + '</td><td>' + item.TMPRMAX + '</td><td>' + '<a href="http://bpr_serv.bmk.by/rel/map.php?cmd=marker(mlat;mlon),(' + item.LAT + ';' + item.LON + ')" target="_blank" title="Нажмите для просмотра на карте">' + item.LAT + ' ' + item.LON + '</a></td></tr>';
						});
						alarmsList += '</tbody></table>';
					}else{
						var alarmsList = 'отсутствуют';
					}

					var alarms = encodeURIComponent(JSON.stringify(result.ALARMS));
					$('#reports').html('<div class="container"><div class="row"><div class="col"><div id="print" class="text-right m-2"><a class = "mr-3" href="reptmpr.php?anum=' + result.ANUM + '&dtbeg=' + result.DTBEG + '&dtend=' + result.DTEND + '&tmprmin=' + result.TMPRMIN + '&tmprmax=' + result.TMPRMAX + '&alarms=' + alarms + '&n=' + Math.random() + '" target="_blank">Открыть в новой вкладке</a><a href="#" onclick="window.print()">Печать</a></div><h1 class="text-center mb-4">Отчет по температуре в кузове</h1><h2>' + result.ANUM + '</h2><h5 class="mb-3">' + result.DTBEG + ' - ' + result.DTEND + '</h5><h5>Минимальная фактическая температура: ' + result.TMPRMIN + ' &deg;C</h5><h5>Максимальная фактическая температура: ' + result.TMPRMAX + ' &deg;C</h5><h5 class="mt-3">Тревоги:</h5>' + alarmsList + '</div></div></div>');

					$('#map').css('display', 'none');
					$('#info').css('display', 'none');
					$('#reports').css('display', 'block');
				}else{
					if (f_gwx  == 'repfuel'){
						if (result.ALARMS.length > 0){
							var alarmsList = '<table class="table table-striped"><thead><tr><th scope="col">Дата и время слива</th><th scope="col">Уровень до, л</th><th scope="col">Слив, л</th><th scope="col">Уровень после, л</th><th scope="col">Координаты</th><th scope="col">Примечание</th></tr></thead><tbody>';
							result.ALARMS.forEach(item => {
								alarmsList += '<tr><td>' + item.DT + '</td><td>' + item.BEFORE + '</td><td>' + item.DRAIN + '</td><td>' + item.AFTER + '</td><td><a href="http://bpr_serv.bmk.by/rel/map.php?cmd=marker(mlat;mlon),(' + item.LAT + ';' + item.LON + ')" target="_blank" title="Нажмите для просмотра на карте">' + item.LAT + ' ' + item.LON + '</a></td><td>' + item.COMMENT + '</td></tr>';
							});
							alarmsList += '</tbody></table>';
						}else{
							var alarmsList = 'отсутствуют';
						}

						var alarms = encodeURIComponent(JSON.stringify(result.ALARMS));
						$('#reports').html('<div class="container"><div class="row"><div class="col"><div id="print" class="text-right m-2"><a class = "mr-3" href="repfuel.php?anum=' + result.ANUM + '&dtbeg=' + result.DTBEG + '&dtend=' + result.DTEND + '&fuel=' + result.FUEL + '&fuelmove=' + result.FUELMOVE + '&fuelstop=' + result.FUELSTOP + '&fuelbeg=' + result.FUELBEG + '&fuelend=' + result.FUELEND + '&lenscan=' + result.LENCAN + '&lengps=' + result.LENGPS + '&lencanbeg=' + result.LENCANBEG + '&lencanend=' + result.LENCANEND + '&refill=' + result.REFILL + '&refillcnt=' + result.REFILLCNT + '&draincnt=' + result.DRAINCNT + '&stopcnt=' + result.STOPCNT + '&movetime=' + result.MOVETIME + '&avgspeed=' + result.AVGSPEED + '&alarms=' + alarms + '&n=' + Math.random() + '" target="_blank">Открыть в новой вкладке</a><a href="#" onclick="window.print()">Печать</a></div><h1 class="text-center mb-4">Отчет по топливу</h1><h2>' + result.ANUM + '</h2><h5 class="mb-3">' + result.DTBEG + ' - ' + result.DTEND + '</h5></div></div><div class="row"><div class="col-md-6"><h5>Потрачено по ДУТ: ' + result.FUEL + ' л</h5><h5>Потрачено по ДУТ в движении: ' + result.FUELMOVE + ' л</h5><h5>Потрачено по ДУТ на холостом ходу: ' + result.FUELSTOP + ' л</h5><h5>Начальный уровень топлива: ' + result.FUELBEG + ' л</h5><h5>Конечный уровень топлива: ' + result.FUELEND + ' л</h5><h5>Пробег CAN: ' + result.LENCAN + ' км</h5><h5>Пробег GPS: ' + result.LENGPS + ' км</h5></div><div class="col-md-6"><h5>Начальный пробег CAN: ' + result.LENCANBEG + ' км</h5><h5>Конечный пробег CAN: ' + result.LENCANEND + ' км</h5><h5>Всего заправлено: ' + result.REFILL + ' л</h5><h5>Всего заправок: ' + result.REFILLCNT + '</h5><h5>Всего сливов: ' + result.DRAINCNT + '</h5><h5>Количество стоянок: ' + result.STOPCNT + '</h5><h5>Время в поездках: ' + result.MOVETIME + '</h5><h5>Средняя скорость: ' + result.AVGSPEED + ' км/ч</h5></div></div><div class="row"><div class="col"><h5 class="mt-3">Сливы:</h5>' + alarmsList + '</div></div></div>');

						$('#map').css('display', 'none');
						$('#info').css('display', 'none');
						$('#reports').css('display', 'block');
					}else{
						if (f_gwx  == 'repsteptmpr'){
							if (result.VALUES.length > 0){
								var alarmsList = '<table class="table table-striped"><thead><tr><th scope="col">Время</th><th scope="col">Tемпература, &deg;C</th></tr></thead><tbody>';
								result.VALUES.forEach(item => {
									alarmsList += '<tr><td>' + item.DT + '</td><td>' + item.TMPR + '</td></tr>';
								});
								alarmsList += '</tbody></table>';
							}else{
								var alarmsList = 'отсутствуют';
							}

							var values = encodeURIComponent(JSON.stringify(result.VALUES));
							$('#reports').html('<div class="container"><div class="row"><div class="col"><div id="print" class="text-right m-2"><a class = "mr-3" href="repsteptmpr.php?anum=' + result.ANUM + '&dtbeg=' + result.DTBEG + '&dtend=' + result.DTEND + '&values=' + values + '&n=' + Math.random() + '" target="_blank">Открыть в новой вкладке</a><a href="#" onclick="window.print()">Печать</a></div><h1 class="text-center mb-4">Отчет по температуре (по интервалам)</h1><h2>' + result.ANUM + '</h2><h5 class="mb-3">' + result.DTBEG + ' - ' + result.DTEND + '</h5><h5 class="mt-3">Температура:</h5>' + alarmsList + '</div></div></div>');

							$('#map').css('display', 'none');
							$('#info').css('display', 'none');
							$('#reports').css('display', 'block');
						}else{
							jQuery('#result').html('<a class = "btn btn-outline-success form-control mb-2" href="reports/'+su_name+rep_pref+'.'+reptype+'?n=' +Math.random()+'" target="_blank" onclick="resclear()">Открыть отчет</a>');
						}
					}
				}
			}
		},
		"json"
	);
};

function resclear(){
	jQuery('#result').html('<input class="btn btn-outline-dark form-control mb-2" id="intrep2367" type="button" value="Сформировать отчет" onclick="frep_all()"/>');
}

function frep9(){	// Функция отчёта по приборам

	var su_name = jQuery('#u_id').val();
	var su_own = jQuery('#u_own').val();

	var f_gwx = 'repdev';

	if (jQuery('#rtype1').attr('checked')){
		var reptype = 'html';
	}
	else{
	if (jQuery('#rtype2').attr('checked')){
		var reptype = 'pdf';
	}
	else{
		var reptype = 'xls';
	}
	}

	jQuery('#progress').html('<img src="images/ajax-loader.gif" width="66px" height="66px"/>');

	var url = 'get_gwx.php';

	jQuery.get(
		url,
		"su_name=" + su_name +"&su_own=" + su_own + "&reptype=" + reptype + "&f_gwx=" + f_gwx,
		function (result){
			if (result.type == 'error'){
				jQuery('#progress').html('');
				alert (result.res);
				return(false);
			}
			else {
				jQuery('#progress').html('');
				jQuery('#result').html('<h1><a href="reports/'+su_name+'RepDev.'+reptype+'?n=' +Math.random()+'" target="_blank">Отчёт сформирован. Для просмотра нажмите на данную ссылку.</a></h1>');
			}
		},
		"json"
	);
};

$(document).ready(function() {

	$('#sour').change(function(){
		$('#dour').val(this.value);
	});

	$("#prev").click(function(){
        $("#prevcontrol").toast({
            autohide: false
        });
        $("#prevcontrol").toast('show');
    });

	$("#roads").click(function(){
        $("#roadscontrol").toast({
            autohide: false
        });
        $("#roadscontrol").toast('show');
    });
	$("#auto_btn_add").click(function(){
		if(jQuery(this).val() !== 'Скрыть форму'){
			jQuery(this).prop('value','Скрыть форму');
			jQuery('#new_auto').css('display','block');
			jQuery('#add_auto').focus();
		}else{
			jQuery(this).prop('value','Добавить машину в список');
			jQuery('#new_auto').css('display','none');
		}
	});
	$("#btn_perev_add").click(function(){
		if(jQuery(this).val() !== 'Скрыть форму'){
			jQuery(this).prop('value','Скрыть форму');
			jQuery('#new_perev').css('display','block');
			jQuery('#perev').focus();
		}else{
			jQuery(this).prop('value','Добавить перевозчика');
			jQuery('#new_perev').css('display','none');
		}
	});


	$('#add_auto').keypress(function(key) {
		if(key.charCode < 48 || (key.charCode > 57 && key.charCode < 65) || key.charCode > 90){
			alert("Номер автомобиля должен состоять только из больших латинских букв и цифр!");
			return false;	
		} 
	});


    $('.accordion').click(function() {
    	var clickId = $(this).attr('aria-controls');
    	if (clickId == 'collapseParams1'){
    		$('#collapseParams2').collapse('hide');
    	}
    	if (clickId == 'collapseParams2'){
    		$('#collapseParams1').collapse('hide');
    	}
  	});

	$("#start").click(function(){
		if (jQuery("#mash").is(':checked') || jQuery("#mvoz").is(':checked')){
			jQuery("#filter_form").submit();
		}else{
			alert("Необходимо выбрать тип автомобиля");
			jQuery("#mash").focus();
			return false;
		}
	});

	$('#collapseReports').on('shown.bs.collapse', function () {
		if ($('#reports').html() > ''){
			$('#map').css('display', 'none');
			$('#info').css('display', 'none');
			$('#reports').css('display', 'block');
		}
	})

	$('#collapseReports').on('hidden.bs.collapse', function () {
		$('#map').css('display', 'block');
		$('#info').css('display', 'block');
		$('#reports').css('display', 'none');
	})

	$("input[type='radio'][name='graf']").on("click", function (e) {
		e.preventDefault();
		setTimeout(
  			() => $(this).prop("checked", !this.checked).trigger("change")
		);
	});

	$('#show_prev').on("click", function () {
		if (this.checked){
			$('#prev').css('display', 'flex');
		}else{
			$('#prev').css('display', 'none');
		}
	});

	// Изменение размеров блоков

	let isResizingX = false;
	let isResizingY = false;
    let lastDownX = 0;
    let lastDownY = 0;

    $('#leftBorder').on('mousedown', function(e) {
        isResizingX = true;
        lastDownX = e.clientX;
    });

	$('#bottomBorder').on('mousedown', function(e) {
        isResizingY = true;
        lastDownY = e.clientY;
    });

    $(document).on('mousemove', function(e) {
        if (isResizingX) {
            let $left = $('#controls');
            let $right = $('#rightside');
            let offset = e.clientX - lastDownX;
            let newLeftWidth = $left.width() + offset;
            let newRightWidth = $right.width() - offset;

            $left.width(newLeftWidth);
            $right.width(newRightWidth);

            lastDownX = e.clientX;
        }

		if (isResizingY) {
            let $top = $('#map');
            let $bottom = $('#info');
            let offset = e.clientY - lastDownY;
            let newTopHeight = $top.height() + offset;
            let newBottomHeight = $bottom.height() - offset;

            $top.height(newTopHeight);
            $bottom.height(newBottomHeight);

            lastDownY = e.clientY;
        }
    });

    $('#leftBorder').on('mouseup', function() {
        isResizingX = false;
		map.invalidateSize();
    });

    $('#bottomBorder').on('mouseup', function() {
        isResizingY = false;
		map.invalidateSize();
    });
});