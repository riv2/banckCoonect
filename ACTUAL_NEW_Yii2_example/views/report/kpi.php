<?php

use app\components\ReportKpiData;
use app\models\reference\Project;

/**
 *  @var \yii\web\View $this
 *  @var Project[] $projects
 *  @var ReportKpiData[] $report
 */
?>

<table class="table table-bordered table-primary  table-hover table-striped">
    <thead>
        <tr>
            <th>Дата расчета</th>
            <th>Проект</th>
            <th>Конкурент</th>
            <th>Средник срок жизни цены</th>
            <th>Всего пересек. SKU</th>
            <th>Собрано цен</th>
            <th>В наличии, кол-во</th>
            <th>Участвовало при расчете</th>
            <th>Не в наличии</th>
            <th>Не собрано</th>
            <th>Статус</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($report as $data) {
                ?>
                <tr>
                    <td><?= $data->createdAt ?></td>
                    <td><?= $data->projectName ?></td>
                    <td><?= $data->competitorName ?></td>
                    <td><?= $data->avgPriceLife ?></td>
                    <td><?= $data->totalCompetitorSku ?></td>
                    <td><?= $data->totalParsed ?></td>
                    <td><?= $data->inStock ?></td>
                    <td><?= $data->tookActionInCalculation ?></td>
                    <td><?= $data->outStock ?></td>
                    <td><?= $data->unparsed ?></td>
                    <td><?= $data->percentMissed ?>%</td>
                </tr>
                <?php
        }
    ?>
    </tbody>
</table>
