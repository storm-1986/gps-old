<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет по топливу</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body>
<?php
if (isset($_GET['anum']) && isset($_GET['dtbeg']) && isset($_GET['dtend']) && isset($_GET['fuel']) && isset($_GET['fuelmove']) && isset($_GET['fuelstop']) && isset($_GET['fuelbeg']) && isset($_GET['fuelend']) && isset($_GET['lenscan']) && isset($_GET['lengps']) && isset($_GET['lencanbeg']) && isset($_GET['lencanend']) && isset($_GET['refill']) && isset($_GET['refillcnt']) && isset($_GET['draincnt']) && isset($_GET['stopcnt']) && isset($_GET['movetime']) && isset($_GET['avgspeed'])&& isset($_GET['alarms'])){
    $anum = $_GET['anum'];
    $dtbeg = $_GET['dtbeg'];
    $dtend = $_GET['dtend'];
    $fuel = $_GET['fuel'];
    $fuelmove = $_GET['fuelmove'];
    $fuelstop = $_GET['fuelstop'];
    $fuelbeg = $_GET['fuelbeg'];
    $fuelend = $_GET['fuelend'];
    $lenscan = $_GET['lenscan'];
    $lengps = $_GET['lengps'];
    $lencanbeg = $_GET['lencanbeg'];
    $lencanend = $_GET['lencanend'];
    $refill = $_GET['refill'];
    $refillcnt = $_GET['refillcnt'];
    $draincnt = $_GET['draincnt'];
    $stopcnt = $_GET['stopcnt'];
    $movetime = $_GET['movetime'];
    $avgspeed = $_GET['avgspeed'];
    $alarms = urldecode($_GET['alarms']);
}else{
?>
    <h1 class="text-center mb-4">Не хватает данных</h1>
<?php
    exit;
}
?>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center mb-4">Отчет по топливу</h1>
                <h2><?= $anum ?></h2>
                <h5 class="mb-3"><?= $dtbeg.' - '.$dtend ?></h5>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h5>Потрачено по ДУТ: <?= $fuel ?> л</h5>
                <h5>Потрачено по ДУТ в движении: <?= $fuelmove ?> л</h5>
                <h5>Потрачено по ДУТ на холостом ходу: <?= $fuelstop ?> л</h5>
                <h5>Начальный уровень топлива: <?= $fuelbeg ?> л</h5>
                <h5>Конечный уровень топлива: <?= $fuelend ?> л</h5>
                <h5>Пробег CAN: <?= $lenscan ?> км</h5>
                <h5>Пробег GPS: <?= $lengps ?> км</h5>
            </div>
            <div class="col-md-6">
                <h5>Начальный пробег CAN: <?= $lencanbeg ?> км</h5>
                <h5>Конечный пробег CAN: <?= $lencanend ?> км</h5>
                <h5>Всего заправлено: <?= $refill ?> л</h5>
                <h5>Всего заправок: <?= $refillcnt ?></h5>
                <h5>Всего сливов: <?= $draincnt ?></h5>
                <h5>Количество стоянок: <?= $stopcnt ?></h5>
                <h5>Время в поездках: <?= $movetime ?></h5>
                <h5>Средняя скорость: <?= $avgspeed ?> км/ч</h5>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h5 class="mt-3">Сливы:</h5>
<?php
                $alarms = json_decode($alarms);
                // print_r($alarms);
                if (count($alarms) > 0){
?>
                    <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">Дата и время слива</th>
                        <th scope="col">Уровень до, л</th>
                        <th scope="col">Слив, л</th>
                        <th scope="col">Уровень после, л</th>
                        <th scope="col">Координаты</th>
                        <th scope="col">Примечание</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                    foreach($alarms as $alarm){
                        echo "<tr><td>$alarm->DT</td><td>$alarm->BEFORE</td><td>$alarm->DRAIN</td><td>$alarm->AFTER</td><td><a href='http://bpr_serv.bmk.by/rel/map.php?cmd=marker(mlat;mlon),($alarm->LAT;$alarm->LON)' target='_blank' title='Нажмите для просмотра на карте'>$alarm->LAT $alarm->LON</a></td><td>$alarm->COMMENT</td></tr>";
                    }
?>
                    </tbody>
                    </table>
<?php
                }else{
                    echo "отсутствуют";
                }
                
?>
            </div>
        </div>
    </div>
    <!-- bootstrap -->
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>