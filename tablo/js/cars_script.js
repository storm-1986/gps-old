function setCookie(name, value, daysToLive) 
{
	var cookie = name + "=" + encodeURIComponent(value);
/*
	if (typeof daysToLive === "number"){
		cookie += "; max-age=" + (daysToLive*60*60*24);
	}else{
		throw new Error('Параметр daysToLive должен быть числом.');
	}
*/
	document.cookie = cookie;
}

function GetCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

function shfiltr(){
	var hflag = 0;
	if ($('#filtr').css('display') !== 'none') hflag = 1;
	$('#filtr').toggle("normal");
	if (hflag == 1){
		$('#ctrlfiltr').html('Раскрыть фильтры');
	}else{
		$('#ctrlfiltr').html('Скрыть фильтры');
	}
}

function set_tr_color(){
	myVar = GetCookie('id_tr');
	myDt = GetCookie('dt_beg');
	myStops = GetCookie('stops');
	if (myVar > "" && myDt > ""){
		$("#"+myVar).css('color','red');
		track(myVar,myDt,myStops);
	}
}

function track(car,dt_beg,stops){
	setCookie('id_tr',car,30);
	setCookie('dt_beg',dt_beg,30);
	setCookie('stops',stops,30);
	window.parent.osm.show_plrtack(car,dt_beg,stops);
	$('tr').css('color','black');
	$("#"+car).css('color','red');
}

function setfiltres(){
$('#mform').submit();
var sList = "";
$('#filtr input[type=checkbox]').each(function () {
/*    var sThisVal = (this.checked ? this.id+"1" : "0");*/
    if (this.checked){
    	if (this.id == 'mol') sThisVal = "ATYPE = 1";
    	if (this.id == 'gryz') sThisVal = "ATYPE = 0";
/*
    	if (this.id == 'st_ok') sThisVal = "STATUS < 101";
    	if (this.id == 'st_er') sThisVal = "STATUS > 100";
*/
    	if (this.id == 'skor0') sThisVal = "VEL = 0";
    	if (this.id == 'skor1-30') sThisVal = "(VEL > 0 AND VEL < 30)";
    	if (this.id == 'skor30-70') sThisVal = "(VEL >= 30 AND VEL < 70)";
    	if (this.id == 'skor70-100') sThisVal = "(VEL >= 70 AND VEL < 100)";
    	if (this.id == 'skor101') sThisVal = "(VEL >= 100)";

		if (this.id.slice(0, 3) == 'fil'){
			console.log(this.id.slice(3));
			sThisVal = "LCTN = " + this.id.slice(3);
		}

    	sList += (sList=="" ? sThisVal : " AND " + sThisVal);
    }else{
    	sThisVal = "";
    }
});

if($("#st_ok").is(':checked')){
	sList += " AND STATUS > 0";
}else{
	if($("#st_er").is(':checked')){
		sList += " AND STATUS > 100";
	}else{
		sList += " AND STATUS < 101";
	}
}

var url = 'get_filters.php';
	jQuery.post(
		url,
		"newq=" + sList,
		function (result){
			if (result.type == 'error'){
				alert(result.msg);
				return(false);
			}
			else{
			$('#mtbl').html(result.ins_tbl);
			}
			},
			"json"
	);
}

function sort(n_sort){
	$('#sort').prop("value",n_sort);
	$('#mform').submit();
}