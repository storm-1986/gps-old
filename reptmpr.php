<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет по температуре в кузове</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body>
<?php
if (isset($_GET['anum']) && isset($_GET['dtbeg']) && isset($_GET['dtend']) && isset($_GET['tmprmin']) && isset($_GET['tmprmax']) && isset($_GET['alarms'])){
    $anum = $_GET['anum'];
    $dtbeg = $_GET['dtbeg'];
    $dtend = $_GET['dtend'];
    $tmprmin = $_GET['tmprmin'];
    $tmprmax = $_GET['tmprmax'];
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
                <h1 class="text-center mb-4">Отчет по температуре в кузове</h1>
                <h2><?= $anum ?></h2>
                <h5 class="mb-3"><?= $dtbeg.' - '.$dtend ?></h5>
                <h5>Минимальная фактическая температура: <?= $tmprmin ?> &deg;C</h5>
                <h5>Максимальная фактическая температура: <?= $tmprmax ?> &deg;C</h5>
                <h5 class="mt-3">Тревоги:</h5>
<?php
                $alarms = json_decode($alarms);
                // print_r($alarms);
                if (count($alarms) > 0){
?>
                    <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">Начало</th>
                        <th scope="col">Конец</th>
                        <th scope="col">T в начале, &deg;C</th>
                        <th scope="col">T в конце, &deg;C</th>
                        <th scope="col">Mин t, &deg;C</th>
                        <th scope="col">Mакс t, &deg;C</th>
                        <th scope="col">Координаты</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                    foreach($alarms as $alarm){
                        echo "<tr><td>$alarm->DTBEG</td><td>$alarm->DTEND</td><td>$alarm->TMPRBEG</td><td>$alarm->TMPREND</td><td>$alarm->TMPRMIN</td><td>$alarm->TMPRMAX</td><td><a href='http://bpr_serv.bmk.by/rel/map.php?cmd=marker(mlat;mlon),($alarm->LAT;$alarm->LON)' target='_blank' title='Нажмите для просмотра на карте'>$alarm->LAT $alarm->LON</a></td></tr>";
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