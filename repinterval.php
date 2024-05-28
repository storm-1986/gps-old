<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет по интервалам движения</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body>
<?php
if (isset($_GET['anum']) && isset($_GET['dtbeg']) && isset($_GET['intervals'])){
    $anum = $_GET['anum'];
    $dtbeg = $_GET['dtbeg'];
    $intervals = urldecode($_GET['intervals']);
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
                <h1 class="text-center mb-4">Отчет по интервалам движения</h1>
                <h2><?= $anum ?></h2>
                <h5 class="mb-3"><?= $dtbeg ?></h5>
<?php
                $intervals = json_decode($intervals);
                if (count($intervals) > 0){
?>
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Интервал</th>
                            <th scope="col">Адрес начальный</th>
                            <th scope="col">Адрес конечный</th>
                            <th scope="col">T нач.</th>
                            <th scope="col">Т кон.</th>
                            <th scope="col">Путь, км</th>
                            <th scope="col">Ср. скор, км/ч</th>
                            <th scope="col">Макс. скор, км/ч</th>
                            <th scope="col">Время движ, ч:мин</th>
                            <th scope="col">Время стоянки, ч:мин</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                    foreach($intervals as $interval){
                        if ($interval->DT > ''){
?>
                            <tr><td colspan='10' class='text-center'><b><?= $interval->DT ?></b></td></tr>
<?php
                        }else{
?>
                            <tr>
                                <td><?= $interval->INTERVAL ?></td>
                                <td><?= $interval->BEGADR ?></td>
                                <td><?= $interval->ENDADR ?></td>
                                <td><?= $interval->TBEG ?></td>
                                <td><?= $interval->TEND ?></td>
                                <td><?= $interval->LEN ?></td>
                                <td><?= $interval->AVVEL ?></td>
                                <td><?= $interval->MAXVEL ?></td>
                                <td><?= $interval->MOVETIME ?></td>
                                <td><?= $interval->STOPTIME ?></td>
                            </tr>
<?php
                        }
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
</body>
</html>