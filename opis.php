<div class="container-fluid">
<div class="h4 my-3">Краткое описание сайта</div>
<div class="mb-2">
<?
	if ($_SESSION['username'] !== 'velcom'){
?>
<b>Внимание!</b> При изменениях в интерфейсе, если что-то некорректно отображается или не работает, попробуйте обновить страницу с помощью комбинации клавиш "CTRL-F5"<br/>
<b>Возможна некорректная работа сайта при использовании браузера Internet Explorer</b>. Рекомендуется использовать <a href="http://www.google.ru/chrome" target="_blank">Chrome</a> или <a href="http://www.opera.com/download/" target="_blank">Opera</a> или <a href="http://www.mozilla.com/ru/firefox/" target="_blank">Firefox</a>.<br/>
<b>Карты OpenStreet.</b> Возможности:
<ul>
	<li>
		Просмотр фактического, планового, обоих маршрутов движения по заданному времени;
	</li>
	<li>
		Просмотр маркеров местоположения машин;
	</li>
	<li>
		Просмотр дополнительной информации (скорость, время передачи последних данных) при нажатии на маркер. Повторное нажатие на маркер убирает окно с информацией;
	</li>
	<li>
		Просмотр маркеров стоянок на проложенном маршруте;
	</li>
	<li>
		Измерение расстояний на карте. При отмеченном параметре "Измерить расстояние" нажатием лефой кнопкой мыши по карте рисуется линия. Повторным нажатием линия "ломается" и рисуется продолжение. В верхнем левом углу появляется информация о расстоянии. Закончить линию можно двойным нажатием левой кнопки мыши. При измерении расстояния карта двигается только стрелками управления в левом верхнем углу. Масштаб можно менять колёсиком мыши, либо перетаскивать ползунок в левом верхнем углу. Для выхода из режима измерения расстояния нужно убрать галочку с параметра "Измерить расстояние";
	</li>
	<li>
		Поиск на карте по координатам (Для поиска нужно нажать на лупу в верхнем левом углу. В появившемся окне ввести широту, долготу и нажать кнопку "Найти". Окно при этом закроется автоматически, а на карте появится маркер);
	</li>
	<li>
		Указание на треке превышений скорости. Для этого после просмотра трека следует нажать на иконку с восклицательным знаком в жёлтом треугольнике (в левом верхнем углу). В появившемся окне указать максимальную скорость, отметить поле и нажать кнопку "Показать". Если были превышения, на некоторых участках трека появятся чёрные точки - маркеры превышения. По нажатии на маркер откроется информация о скорости.
	</li>
	<li>
		Формирование отчётов: <b>по интервалам движения</b> (время и адрес начала интервала, время и адрес конец интервала, расстояние пути, средняя скорость, максимальная скорость, время стоянки), <b>по стоянкам</b> (время начала движения/стоянки, время конца движения/стоянки, длительность движения/стоянки, средняя скорость движения, длина пути в движении, адрес стоянки), <b>сравнительный отчёт</b> (№ точки, № рейса, № дня и время плановой остановки, тип плановой остановки (погрузка/разгрузка), № дня и время начала фактической остановки и длительность фактической остановки, адрес точки)
	</li>
</ul>
<div>
	Треки стали более информативными! Появились указатели начала, конца, направление трека (стрелки только на участках, где скорость машины не меньше 35км/ч)!
</div>
<div>
	Перемещение карты, изменение масштаба, всплывающие окна при нажатии на маркеры - внутри области панели управления не работают. Спрятать панель управления можно, нажав на крестик в правом верхнем углу панели. При этом на левой стороне карты появляется значок "плюс" на синем фоне. Нажав на него, можно снова открыть панель управления.
</div>
<div class="mb-2">
	Раздел меню <b>"Сервисы"</b> Содержит список автомобилей, от которых есть данные за текущие сутки, список автомобилей с нерабочей навигацией, движение выбранного автомобиля в реальном времени. Состоит из следующих подразделов:
	<ul>
		<li>Машины с данными за сегодня - возможность просмотра состояния автомобилей, от которых были данные за текущие сутки</li>
		<li>Нерабочие навигации - список автомобилей, от которых не было данных за последние сутки</li>
		<li>Перемещение online - возможность просмотра маршрута автомобиля в режиме реального времени</li>
	</ul>
	Выберите период обновления данных и нажмите кнопку "Включить мониторинг". Для изменения периода обновления необходимо сначала выключить мониторинг, выбрать новый период и снова включить мониторинг. 
</div>
<div class="mb-2">
	<b>Обновление!</b> При отмеченном поле "Считать полные сутки" расчёт треков, отчётов производится за целые сутки указанной даты (с 00:00:00 по 23:59:59).
</div>
<div class="mb-2">
	Для выхода из сайта нажмите на ссылку <b>"Выход"</b> в верхнем правом углу экрана.
</div>
<div class="mb-2">
	С вопросами по сайту обращаться по внутреннему телефону 464 (Вадим). Предложения и замечания присылать на <a href="mailto:shtorm@pda.savushkin.by">shtorm@pda.savushkin.by</a>.
<?
}else{
?>
<div>
	Для просмотра ферм откройте вкладку "Карты OpenStreet" и нажмите кнопку "Показать фермы". При нажатии на точку фермы появляется всплывающее окно с названием фермы. Закрыть всплывающее окно можно повторным нажатием на точку фермы. Панель с кнопкой можно скрыть нажатием на крестик. Также можно выбрать другую карту (гугл карта, гугл спутник). Для этого нажмите на плюсик в правом верхнем углу карты и выберите нужную карту.
</div>
<div>
	P.S.<br/>
	Рекомендуется использовать браузер "Google Chrome".
</div>
<?
}
?>
</div>
</div>
</div>