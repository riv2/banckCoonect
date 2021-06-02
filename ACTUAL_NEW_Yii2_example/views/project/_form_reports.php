<?php
use app\models\reference\Project;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * @var ActiveForm $form
 * @var Project $model
 * @var array $projectExecutionList
 * @var string $projectExecutionId
 * @var \yii\web\View $this
 */

?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs report-links">
        <li class=""><a href="<?=Url::to(['/crud-price-calculated','iframe' => 1, 'PriceCalculated[project_execution_id]' => $projectExecutionId])?>" type="button" class="" target="reports-iframe">Цены</a></li>
        <li class=""><a href="<?=Url::to(['/crud-log-project-execution','iframe' => 1, 'LogProjectExecution[is_export]' => 0, 'search[project_execution_id]' => $projectExecutionId])?>" type="button" class="" target="reports-iframe">История цен</a></li>
        <li class=""><a href="<?=Url::to(['/crud-log-price-calculation','iframe' => 1, 'LogPriceCalculation[project_execution_id]' => $projectExecutionId, 'sort' => 'item_id'])?>" type="button" class="" target="reports-iframe">История расчета цен</a></li>
        <li class=""><a href="<?=Url::to(['/report/rrp-violations','iframe' => 1, 'project_execution_id' => $projectExecutionId])?>" type="button" class="" target="reports-iframe">Нарушения РРЦ</a></li>
        <li class=""><a href="<?=Url::to(['/crud-report-keywords-control/','iframe' => 1, 'ReportKeywordsControl[project_execution_id]' => $projectExecutionId])?>" type="button" class="" target="reports-iframe">Контроль КС</a></li>
        <li class=""><a href="<?=Url::to(['/crud-report-calculation-overview/','iframe' => 1, 'ReportCalculationOverview[project_execution_id]' => $projectExecutionId])?>" type="button" class="" target="reports-iframe">Обзорная таблица</a></li>
        <li class="pull-right">
            <?= Html::dropDownList('project_execution_id', $projectExecutionId, $projectExecutionList, [
                'class' => 'form-control',
                'id' => 'reports-project_execution_id'
            ]) ?>
        </li>
    </ul>
    <div style="height: 4px;"></div>
    <iframe id="reports-iframe" name="reports-iframe" src="" style="border: none; width: 100%; height: 100px; overflow: scroll;" onload=""></iframe>
</div>
<?php

$this->registerJs("
function iframeFit() {
    var ifr = jQuery('#reports-iframe');
    ifr.css('height', jQuery(window).height() - 80 + jQuery(window).scrollTop() - ifr.offset().top);
}
iframeFit();
jQuery(window).resize(iframeFit);
jQuery(window).scroll(iframeFit);
");