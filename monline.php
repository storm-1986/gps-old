<div id="progress"></div>
<div id="all" class="container-fluid">
    <div class="toast" id="infoonlroute" style="position: absolute; top: 110px; left: 60px; z-index: 10000;">
        <div class="toast-header">
            <strong class="mr-auto">Информация о маршруте</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
        	<div id="insinfoonlroute"></div>
        </div>
    </div>
<div id="dr2" class="row">
<div id="map"></div>
</div>
</div>
<div id="shcont" class="rlabel" onclick="showhide('onlcontent','shcont')"></div>
<div id="onlcontent">
<button type="button" class="close mr-2" aria-label="Close" onclick="showhide('onlcontent','shcont')">
	<span aria-hidden="true">&times;</span>
</button>
	<div class="row">
	<div class="col-12">
		<div class="form-group mt-2">
			<label>Выбор машины:</label>
			<? include 'selcars.php'; ?>
		</div>
		<div class="form-group">
			<label>Период обновления:</label>
			<select id="onlint" size="1" class="form-control">
				<option value="10000">10 c</option>
				<option value="15000">15 c</option>
				<option value="30000">30 c</option>
				<option value="60000">1 мин</option>
				<option value="90000">1,5 мин</option>
				<option value="120000">2 мин</option>
				<option value="300000">5 мин</option>
				<option value="600000">10 мин</option>
				<option value="900000">15 мин</option>
				<option value="1800000">30 мин</option>
			</select>
		</div>
	</div>
	<div class="col-6">
		<div class="form-group">
			<input id="onlstart" type="button" class="btn btn-outline-success" value="Включить мониторинг" onclick="autonline()"/>
		</div>
	</div>
	<div class="col-6 text-right">
		<div class="form-group">
			<input id="onlstop" type="button" class="btn btn-outline-danger" value="Выключить мониторинг" onclick="autonlinestop()" disabled="disable"/>
		</div>
	</div>
	</div>
</div>