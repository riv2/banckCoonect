<?php
use app\models\reference\Project;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var ActiveForm $form
 * @var Project $model
 * @var array $groupedProjectItems
 * @var \yii\web\View $this
 */

?>
<div class="margin">
    <div class="col-xs-2">
        <?= \app\widgets\FormBuilder::renderSelect2($this,
            \app\models\reference\NomenclatureDocument::className(),
            \yii\helpers\Html::getInputName($model, 'nomenclature_document_id'),
            $model->nomenclature_document_id,
            false
        ) ?>
    </div>
    <div class="col-xs-3">
        <?=  Html::a(
            '<span class="glyphicon glyphicon-import"></span> Импортировать',
            [
                '/crud-nomenclature-document-item/import',
                'NomenclatureDocumentItem[nomenclature_document_id]' => $model->nomenclature_document_id
            ], [
                'class' => 'btn btn-default'
            ]
        ); ?>
        <?=
        Html::a('<span class="glyphicon glyphicon-import"></span> Импортировать вручную',['/crud-project-item/import', 'ProjectItem[project_id]' => $model->id, 'exclude' => 'name,id'], [
            'class' => 'btn btn-default'
        ])
        ?>
    </div>
    &nbsp;
    <span style="display: inline-block; width: 150px; text-align: right;">
        Список URL'ов:&nbsp;
    </span>
    <?=  Html::a('<span class="fa fa-yc-square"></span> YM',['/api/cd/ym-urls', 'id' => $model->id,], [
            'class' => 'btn btn-default',
            'target' => '_blank',
        ]
    ); ?>
    <?=  Html::a('<span class="fa fa-globe"></span> Конкурентов ',['/api/cd/competitor-urls', 'id' => $model->id,], [
            'class' => 'btn btn-default',
            'target' => '_blank',
        ]
    ); ?>
</div>

<div id="project-items-async">
    <div class="text-center  margin40">
        <span class="fa fa-spinner fa-spin page-loading-indicator"></span>
    </div>
    <?php
    $this->registerJs("$.ajax({
            'url'       : '/project/project-items-async',
            'data'      : {
                'id' : '{$model->id}'
            },
            'dataType'  : 'html',
            'success'   : function (html) {
                $('#project-items-async').html(html);
                initProjectItemsGrouped();
            }
    });");
    ?>
</div>