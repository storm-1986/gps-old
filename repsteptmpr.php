<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет по температуре (по интервалам)</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body>
<?php
if (isset($_GET['anum']) && isset($_GET['dtbeg']) && isset($_GET['dtend']) && isset($_GET['values'])){
    $anum = $_GET['anum'];
    $dtbeg = $_GET['dtbeg'];
    $dtend = $_GET['dtend'];
    $values = urldecode($_GET['values']);
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
                <h1 class="text-center mb-4">Отчет по температуре (по интервалам)</h1>
                <h2><?= $anum ?></h2>
                <h5 class="mb-3"><?= $dtbeg.' - '.$dtend ?></h5>
                <h5 class="mt-3">Температура:</h5>
<?php
                $values = json_decode($values);
                // print_r($alarms);
                if (count($values) > 0){
?>
                    <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">Время</th>
                        <th scope="col">Tемпература, &deg;C</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                    foreach($values as $value){
                        echo "<tr><td>$value->DT</td><td>$value->TMPR</td></tr>";
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