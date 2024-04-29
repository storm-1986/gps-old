<div id="progress"></div>
<!--
<div class="toast hide" id="findcontrol" style="position: absolute; top: 105px; left: 250px; z-index: 10000;" onclick="push_to_fwd(this.id)">
    <div class="toast-header">
        <strong class="mr-auto">Поиск</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body">
        <p class="h6 mb-3">Поиск по координатам</p>
   		<div class="form-group">
   			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1">Широта</span>
				</div>
				<input type="text" class="form-control" id="slat" placeholder="5x.xx" aria-label="Широта" aria-describedby="basic-addon1" maxlength="9" onkeypress="return numReal(event)">
			</div>
   			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1">Долгота</span>
				</div>
				<input type="text" class="form-control" id="slong" placeholder="2x.xx" aria-label="Долгота" aria-describedby="basic-addon2" maxlength="9" onkeypress="return numReal(event)">
			</div>
			<input type="button" class="btn btn-secondary form-control" value="Найти" onclick="Search2()"/>
   		</div>
   		<p class="h6 mb-3">Поиск по адресу</p>
   		<div class="form-group">
			<input type="text" class="form-control mb-3" id="city" placeholder="Город" onkeydown="search_osm_ent(event)">
			<input type="text" class="form-control mb-3" id="street" placeholder="Улица" onkeydown="search_osm_ent(event)">
			<input type="text" class="form-control mb-3" id="house" placeholder="Здание" onkeydown="search_osm_ent(event)">
			<input type="button" class="btn btn-secondary form-control" value="Найти" onclick="Search_osm()"/>
   		</div>
    </div>
</div>
-->
<div id="all">
    <div id="controls" class="resizable">
        <div class="p-2">
            <?php include 'selcars.php'; ?>
        </div>
        <?php include 'date.htm'; ?>
        <div class="accordion" id="accordionLeftMenu">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <a class="nav-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Треки</a>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionLeftMenu">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-check form-check">
                                <input type="checkbox" class="form-check-input" name="all_day" id="all_day" checked="checked"/>
                                <label for="all_day" class="form-check-label rlabel font-weight-bold">Полные сутки</label>
                            </div>
                            <div class="form-check form-check">
                                <input type="checkbox" class="form-check-input" name="show_stops" id="show_stops">
                                <label for="show_stops" class="form-check-label rlabel font-weight-bold">Стоянки</label>
                            </div>
                            <div class="form-check form-check">
                                <input type="checkbox" class="form-check-input" name="show_prev" id="show_prev">
                                <label for="show_prev" class="form-check-label rlabel font-weight-bold">Превышения</label>
                            </div>
                            <div id="prev" class="input-group mt-1 mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">более</span>
                            </div>
                            <input type="text" class="form-control" value="90" id="prev_skor">
                            <div class="input-group-append">
                                <span class="input-group-text">км/ч</span>
                            </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="graf" id="show_skor">
                                <label for="show_skor" class="form-check-label rlabel font-weight-bold">График скорости</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="graf" id="show_tpl">
                                <label for="show_tpl" class="form-check-label rlabel font-weight-bold">График топлива</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="graf" id="show_tmpr">
                                <label for="show_tmpr" class="form-check-label rlabel font-weight-bold">График температуры</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="btn-group colors" role="group" aria-label="Цвет маршрута">
                                    <button type="button" class="btn btn-primary trcol" title="Цвет маршрута: синий" onclick="chcolor('blue')">&nbsp;</button>
                                    <button type="button" class="btn btn-danger trcol" title="Цвет маршрута: красный" onclick="chcolor('red')">&nbsp;</button>
                                    <button type="button" class="btn btn-success trcol" title="Цвет маршрута: зеленый" onclick="chcolor('green')">&nbsp;</button>
                                    <button type="button" class="btn btn-warning trcol" title="Цвет маршрута: желтый" onclick="chcolor('yellow')">&nbsp;</button>
                                    <button type="button" class="btn btn-dark trcol" title="Цвет маршрута: черный" onclick="chcolor('black')">&nbsp;</button>
                                </div>
                            </div>
                            <input type="hidden" name="colortrack" id="colortrack" value=""/>
                        </div>
                        <div class="form-group">
                            <select class="form-control mb-2" id="typetrack" onchange="sh_smena(this.value)">
                                <option selected="selected" value="0">Фактический</option>
                                <option value="1">Плановый</option>
                                <option value="2">Оба</option>
                            </select>
                        </div>
                        <div id="smena" class="form-group">
                            <input type="text" class="form-control" placeholder="смена" id="n_sm"/>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-outline-dark form-control btn-sm mb-2" id="ajtracks" type="button" name="addtrack" value="Показать маршрут" onclick="osmtr(1,'none')"/>
                            <input class="btn btn-outline-dark form-control btn-sm mb-2" id="ajcars" type="button" name="gcars" value="Показать где машина" onclick="OSCarOnMap()"/>
                            <!-- <input class="btn btn-outline-dark form-control mb-2" id="ajallcars" type="button" name="gallcars" value="Все машины" onclick="AllCarsOnMap()"/> -->
                            <input type="hidden" name="lineToggle" value="0" id="lineToggle" />
                        </div>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingReports">
                    <a class="nav-link" data-toggle="collapse" data-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">Отчеты</a>
                </div>
                <div id="collapseReports" class="collapse" aria-labelledby="headingReports" data-parent="#accordionLeftMenu">
                <div class="card-body">
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report" id="rep-int" value="1" disabled>
                            <label class="form-check-label rlabel" for="rep-int">По интервалам движения</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report" id="rep-stop" value="2" disabled>
                            <label class="form-check-label rlabel" for="rep-stop">По стоянкам</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report" id="rep-sravn" value="3" disabled>
                            <label class="form-check-label rlabel" for="rep-sravn">Сравнительный</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report" id="rep-ptmp" value="4" checked>
                            <label class="form-check-label rlabel" for="rep-ptmp">По температуре (превышения)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report" id="rep-itmp" value="5">
                            <label class="form-check-label rlabel" for="rep-itmp">По температуре (интервалы)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report" id="rep-tpl" value="6">
                            <label class="form-check-label rlabel" for="rep-tpl">По топливу</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div id="result">
                            <input class="btn btn-outline-dark form-control btn-sm mb-2" id="intrep2367" type="button" value="Сформировать отчет" onclick="frep_all()"/>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingGroups">
                    <a class="nav-link" data-toggle="collapse" data-target="#collapseGroups" aria-expanded="false" aria-controls="collapseGroups">Группы машин</a>
                </div>
                <div id="collapseGroups" class="collapse" aria-labelledby="headingGroups" data-parent="#accordionLeftMenu">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="mash" class="form-check-input rlabel" checked="checked" /><label for="mash" class="form-check-label rlabel">Машины</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="mvoz" class="form-check-input rlabel" checked="checked" /><label for="mvoz" class="form-check-label rlabel">Молоковозы</label>
                            </div>
                        </div>
                        <?php
                            if ($_SESSION['userowner'] == 0){
                        ?>
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="sav" class="form-check-input rlabel"/><label for="sav" class="form-check-label rlabel">Только машины Савушкин</label>
                                    </div>
                                </div>
                        <?php
                            }
                        ?>
                        <div class="form-group" id="group_lctn">
                        <?php
                            $list_lctn = explode(",", $_SESSION['lctn']);

                            $list_q = "SELECT LCTN, DSCR FROM SP_LCTN";
                            $res_listq = $conn->query($list_q);
                            $i = 0;
                            while($data_listq = $res_listq->fetch( PDO::FETCH_ASSOC )){
                                $reslctn = $data_listq["LCTN"];
                                $resname = trim($data_listq["DSCR"]);
                                $i == 0 ? $ch = 'checked="checked"' : $ch = '';
                                if (in_array($reslctn, $list_lctn)){
                                    echo "<div class=\"form-check form-check-inline\"><input type=\"checkbox\" name=\"lctn$reslctn\" id=\"lctn$reslctn\" value=\"$reslctn\" class=\"form-check-input gr-lctn\" $ch/><label for=\"lctn$reslctn\" class=\"form-check-label rlabel\">$resname</label></div>";
                                    $i++;
                                }
                            }
                        ?>
                        </div>
                        <input class="btn btn-outline-dark form-control btn-sm mb-2" id="ajtracks" type="button" name="addtrack" value="Показать группу" onclick="showgroup()"/>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingDist">
                    <a class="nav-link" data-toggle="collapse" data-target="#collapseDist" aria-expanded="false" aria-controls="collapseDist">Расстояние по дорогам</a>
                </div>
                <div id="collapseDist" class="collapse" aria-labelledby="headingDist" data-parent="#accordionLeftMenu">
                    <div class="card-body">
                        <div class="form-group">
   			                <label>Введите координаты населенных пунктов:</label>
			                <input type="text" class="form-control" id="searchadrroad1" placeholder="От" maxlength="30">
        	                <div class="form-check form-group">
        		                <input class="form-check-input rlabel" type="checkbox" id="phand1" onchange="handpoint(1,1)"/>
				                <label class="form-check-label rlabel" for="phand1">Указать на карте</label>
        	                </div>
		                </div>
   		                <div class="form-group">
			                <select class="form-control d-none" id="arraddr1" onchange="newobj(1)"></select>
		                </div>
		                <div id="instransit"></div>
   		                <div class="form-group">
			                <input type="text" class="form-control" id="searchadrroad2" placeholder="До" maxlength="30">
        	                <div class="form-check form-group">
        		                <input class="form-check-input rlabel" type="checkbox" id="phand2" onchange="handpoint(2,2)"/>
				                <label class="form-check-label rlabel" for="phand2">Указать на карте</label>
        	                </div>
		                </div>
   		                <div class="form-group">
			                <select class="form-control d-none" id="arraddr2" onchange="newobj(2)"></select>
		                </div>
                        <div id="rasst" class="h5"></div>
		                <input type="hidden" id="trcounter" value="3"/>
		                <div class="row mb-3">
                            <div class="col-6">
        	                    <button type="button" class="btn btn-outline-success btn-sm" onclick="transit()">Добавить точку</button>
                            </div>
                            <div class="col-6 text-right">
        	                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="mtransit()">Удалить точку</button>
                            </div>
                        </div>
                        <div class="form-group">
			                <input type="button" class="btn btn-secondary form-control" id="rasstroadbtn" value="Рассчитать" onclick="rasst_clm()"/>
		                </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group p-2">
            <input class="btn btn-outline-dark form-control btn-sm" type="button" name="remgcars" value="Очистить карту" onclick="OSRemAllFromMap()"/>
        </div>
    </div>
    <div id="leftBorder"></div>
    <div id="rightside" class="resizable">
        <!--
        <div id="imgbtns">
            <input id="mfound" class="mx-1" type="image" src="/images/lypa.png" alt="Поиск по адресу и координатам" title="Поиск по адресу и координатам">
        </div>
        -->
        <div id="reports"></div>
        <div id="map"></div>
        <div id="bottomBorder"></div>
        <div id="info">
            <div id="infoblock">
                <ul class="nav nav-tabs" id="infoTab" role="tablist">
                </ul>
                <div class="tab-content" id="infoTabContent">
                </div>
            </div>
        </div>
    </div>
</div>
