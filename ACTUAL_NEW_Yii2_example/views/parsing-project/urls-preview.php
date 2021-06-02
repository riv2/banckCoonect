<?php
/**
 * @var array $items
 * @var \app\models\reference\ParsingProject $parsingProject
 * @var \yii\web\View $this
 */
use yii\helpers\Json;

$masks = $parsingProject->getProjectMasks();
$this->title = "УРЛ для проектра парсинга &laquo;$parsingProject&raquo;";
?>
<h1><?=$this->title?></h1>
<h3>Урлов: <?=count($items)?></h3>
<div class="box">
    <div class="box-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>УРЛ</th>
                <th>Доп. параметры</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item['url'] ?></td>
                    <td><?= Json::encode($item['attributes']) ?></td>
                    <td><?= isset($item['must'])  && $item['must'] ? "Must: \"{$item['must']}\"": null?> <?= isset($item['dont']) && $item['dont'] ? "Dont: \"{$item['dont']}\"": null?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
