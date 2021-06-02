<?php
use app\models\enum\ProjectExecutionStatus;
use app\models\reference\Project;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var ActiveForm $form
 * @var Project $model
 * @var array $projectExecutionList
 * @var string $projectExecutionId
 */

?>
<div class="row" style="margin-bottom:20px;">
    <div class="col-sm-4">
        <?= Html::dropDownList('project_execution_id', $projectExecutionId, $projectExecutionList, [
            'class' => 'form-control',
            'id' => 'prices-project_execution_id'
        ]) ?>
    </div>
    <div class="col-sm-4">
        <?php
        $unblocked = '';
        $blocked = 'display:none;';
        if (!in_array($model->project_execution_status_id, [ProjectExecutionStatus::STATUS_NEW, ProjectExecutionStatus::STATUS_READY, ProjectExecutionStatus::STATUS_EXPORTED, ProjectExecutionStatus::STATUS_CALCULATED]) ) {
            $unblocked = 'display:none;';
            $blocked = '';
        }
        ?>

        <?php
            if (!$model->projectPriceFormerTypes || count($model->projectPriceFormerTypes) === 0) {
                echo '<a href="#" class="btn btn-default project-on-unblocked" disabled="true" style="'.$unblocked.'"><span class="glyphicon glyphicon-cloud-upload"></span> Выгрузка не возможна - не указан тип цены</a>';
            } else {
                echo Html::a(
                    '<span class="glyphicon glyphicon-cloud-upload"></span> Выгрузить в PriceFormer',
                    ['export-prices', 'project_execution_id' => $projectExecutionId],
                    [
                        'class' => 'btn btn-warning prices-export project-on-unblocked',
                        'style' => $unblocked,
                    ]
                );
            }
            echo '<a href="#" class="btn btn-default project-on-blocked" disabled="true" style="'.$blocked.'"><span class="glyphicon glyphicon-cloud-upload"></span> Выгрузка не возможна - идет процесс</a>';
        ?>
    </div>
</div>
<iframe id="prices-iframe" name="prices-iframe" data-src="<?=\yii\helpers\Url::to(['/project/prices-report','iframe' => 1, 'project_execution_id' => $projectExecutionId])?>" style="border: none; width: 100%; height: 1000px;" onload="this.height = this.contentWindow.document.body.scrollHeight"></iframe>