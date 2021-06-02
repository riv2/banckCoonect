<?php

/**
 * @var \yii\web\View $this
 * @var array $competitorIds
 * @var array $competitors
 * @var array $projectIds
 * @var array $projects
 * @var \app\models\reference\ProjectCompetitor[] $models
 */

use app\assets\ProjectCompetitorIndexAsset;
use app\models\enum\Status;
use app\models\reference\Brand;
use app\models\reference\Category;
use app\models\reference\ProjectCompetitor;
use app\widgets\FormBuilder;
use maddoger\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\LinkPager;

ProjectCompetitorIndexAsset::register($this);

$this->registerJs('window.projectCompetitors = '.Json::encode($models).';', View::POS_HEAD);

$model = new ProjectCompetitor;
$model->loadDefaultValues();
?>
<h1>Конкуренты проектов</h1>
<form class="form form-inline">
    <div class="row">


        <div class="col-sm-5" style="margin-bottom: 10px;">
            <?php
            echo Select2::widget([
                'name'          => 'competitorIds',
                'value'         => $competitorIds,
                'clientOptions' => [
                    'width'             => '100%',
                    'allowClear'        => true,
                    'closeOnSelect'     => true,
                    'minimumInputLength' => 0,
                    'multiple'           => true,
                    'data'              => $competitors,
                ],
                'clientEvents' => null,
                'options' => [
                    'id'            =>  'competitorIds',
                    'class'         => 'select2',
                    'value'         => $competitorIds,
                    'placeholder'   => 'Конкуренты',
                ],
            ]);
            ?>
        </div>

        <div class="col-sm-5" style="margin-bottom: 10px;">
            <?php
            echo Select2::widget([
                'name'          => 'projectIds',
                'value'         => $projectIds,
                'clientOptions' => [
                    'width'             => '100%',
                    'allowClear'        => true,
                    'closeOnSelect'     => true,
                    'minimumInputLength' => 0,
                    'multiple'           => true,
                    'data'              => $projects,
                ],
                'clientEvents' => null,
                'options' => [
                    'id'            =>  'projectIds',
                    'class'         => 'select2',
                    'value'         => $projectIds,
                    'placeholder'   => 'Проекты',
                ],
            ]);
            ?>
        </div>


        <div class="col-sm-2 text-right">
            <?=Html::submitButton('Применить',['class' => 'btn btn-primary']);?>
        </div>

    </div>
    <?php if ($dataProvider): ?>
    <div class="row">
        <div class="col-sm-8">
            <?php
            echo LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'pageCssClass' => 'horadric-pagination',
                'prevPageCssClass' => 'prev',
                'nextPageCssClass' => 'next',
            ]);
            ?>
        </div>
    </div>
    <?php endif; ?>

</form>
<?php if (!empty($models)) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-10">
                Конкуренты проектов
            </div>
            <?php if ($dataProvider): ?>
                    <div class="col-xs-2 text-right">
                        <nobr>
                        <?=$dataProvider->pagination->totalCount?> всего
                        </nobr>
                    </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
            <tr>
                <th style="display: none;"><i class="fa fa-check-square-o" aria-hidden="true"></i></th>
                <th>Конкурент</th>
                <th>Проект</th>
                <th><i class="fa fa-key" aria-hidden="true"></i></th>
                <th>Отклонение</th>
                <th>Изменить РЦ на</th>
                <th>Бренды</th>
                <th>Категории</th>
                <th>Откл.</th>
            </tr>
            </thead>
            <tbody id="competitors-rows" >
                <tr id="project_competitor-template" style="display: none;">
                    <td style="display: none;">
                        <?= Html::checkbox("ProjectCompetitors[]", false, [
                            'class' => 'project_competitor-select',
                            'value' => '%id%',
                        ])?>
                    </td>
                    <td class="project_competitor-competitorName"></td>
                    <td class="project_competitor-projectName"></td>
                    <td>
                        <label for="%id%" class="td-check">
                            <?= Html::checkbox("ProjectCompetitor[%id%][is_key_competitor]", $model->is_key_competitor, [
                                'id'    => "ProjectCompetitor[%id%][is_key_competitor]",
                                'class' => 'project_competitor-key'
                            ])?>
                        </label>
                    </td>
                    <td>
                        <?= Html::dropDownList("ProjectCompetitor[%id%][price_variation_modifier]", $model->price_variation_modifier, [
                            '0%','1%','2%','3%','4%','5%','6%','7%','8%','9%','10%'
                        ], [
                            'data-field' => 'price_variation_modifier',
                            'class' => 'project_competitor-price_variation_modifier',
                            'prompt'=>'Выкл.',
                        ]);?>
                    </td>
                    <td>
                        <?php
                         $ll = [];
                         for ($i = -10; $i <= 10; $i++) {
                             $ll[(string)$i] = $i.'%';
                         }
                        ?>
                        <?=Html::dropDownList("ProjectCompetitor[%id%][price_final_modifier]",(string)$model->price_final_modifier,$ll, [
                            'data-field' => 'price_final_modifier',
                            'class' => 'project_competitor-price_final_modifier',
                            'prompt'=>'Выкл.',
                        ]);?>
                    </td>
                    <td>
                        <?=Html::a('Бренды', '#',[
                            'class'                 => 'btn btn-default btn-sm brandsShow',
                            'data-id'               => '%id%',
                            'style'                 => 'white-space: normal; text-align: left;',
                        ])?>
                        <?=Html::hiddenInput("ProjectCompetitorBrands[%id%][brandsSelected]", '', [
                            'class' => 'project_competitor-brands_selected'
                        ]);?>
                        <?=Html::hiddenInput("ProjectCompetitorBrands[%id%][brandsBanned]", '', [
                            'class' => 'project_competitor-brands_banned'
                        ]);?>
                    </td>
                    <td>
                        <?=Html::a('Категории', '#',[
                            'class'                 => 'btn btn-default btn-sm categoriesShow',
                            'data-id'               => '%id%',
                            'style'                 => 'white-space: normal; text-align: left;',
                        ])?>
                        <?=Html::hiddenInput("ProjectCompetitorCategories[%id%][categoriesSelected]", '', [
                            'class' => 'project_competitor-categories_selected'
                        ]);?>
                        <?=Html::hiddenInput("ProjectCompetitorCategories[%id%][categoriesBanned]", '', [
                            'class' => 'project_competitor-categories_banned'
                        ]);?>
                    </td>
                    <td>
                        <?= Html::checkbox("ProjectCompetitor[%id%][status_id]", $model->status_id !== Status::STATUS_ACTIVE, [
                            'id'    => "ProjectCompetitor[%id%][status_id]",
                            'class' => 'project_competitor-status',
                            'value' => 2
                        ])?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="well">
    Выберите конкурентов или проекты в фильтрах
</div>
<?php endif ?>
<!-- Modal -->
<div class="modal fade" id="selectBrandsModal" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Выбрать или искючить бренды у <span class="competitorName"></span></h4>
            </div>
            <div class="modal-body">
                <?=FormBuilder::renderSelect2($this, Brand::className(), 'select_brands', null, false ) ?>
                <textarea name="select_brands_text" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-danger selectBan"><span class="glyphicon glyphicon-ban-circle"></span> Исключить</button>
                <button type="button" class="btn btn-success selectOk"><span class="glyphicon glyphicon-check"></span> Выбрать</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="selectCategoriesModal" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Выбрать или искючить категории у <span class="competitorName"></span></h4>
            </div>
            <div class="modal-body">
                <?=FormBuilder::renderSelect2($this, Category::className(), 'select_categories', null, true ) ?>
                <textarea name="select_categories_text" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-danger selectBan"><span class="glyphicon glyphicon-ban-circle"></span> Исключить</button>
                <button type="button" class="btn btn-success selectOk"><span class="glyphicon glyphicon-check"></span> Выбрать</button>
            </div>
        </div>
    </div>
</div>