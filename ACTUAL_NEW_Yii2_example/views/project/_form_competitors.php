<?php
use app\models\enum\CompetitionMode;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\reference\Competitor;
use app\models\reference\Project;
use app\models\reference\ProjectCompetitor;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Json;
use \yii\web\View;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var Project $model
 * @var ProjectCompetitor[] $competitors
 */
$projectCompetitors = ProjectCompetitor::find()
    ->alias('t')
    ->joinWith('competitor')
    ->joinWith('projectCompetitorCategories')
    ->joinWith('projectCompetitorBrands')
    ->joinWith('projectCompetitorItems')
    ->andWhere(['t.project_id' => $model->id])
    ->orderBy([Competitor::tableName().'.name' => SORT_ASC])
    ->indexBy('competitor_id')
    ->asArray()
    ->all();

$this->registerJs("window.projectCompetitors = ".Json::encode($projectCompetitors).";", View::POS_HEAD);

$projectCompetitor = new ProjectCompetitor;
$projectCompetitor->loadDefaultValues();

?>
<?= $form->field($model, 'competition_mode_id')->dropDownList(CompetitionMode::getEnumList()); ?>
<div class="panel panel-primary">
    <div class="panel-heading">Конкуренты</div>
    <div class="panel-body">
        <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-search"></span></div>
            <input id="competitors-filter" type="text" class="form-control" placeholder="Фильтр по имени" value="">
        </div><!-- /input-group -->
        <table class="table">
            <thead>
            <tr>
                <th><i class="fa fa-check-square-o" aria-hidden="true"></i></th>
                <th>Название</th>
                <th><i class="fa fa-key" aria-hidden="true"></i></th>
                <th>Отклонение</th>
                <th>Изменить РЦ на</th>
                <th>Собрать</th>
                <th>Бренды</th>
                <th>Категории</th>
                <th>Исключенные товары</th>
                <th>Откл.</th>
            </tr>
            </thead>
            <tbody id="competitors-rows" >
                <tr id="project_competitor-template" class="" data-name="" data-competitor_id="" style="display: none;">
                    <td>
                        <?= Html::checkbox("ProjectCompetitors[]", false, [
                            'class' => 'project_competitor-select',
                            'value' => '%competitor-id%',
                        ])?>
                    </td>
                    <td class="project_competitor-name"></td>
                    <td>
                        <label for="%competitor-id%" class="td-check">
                            <?= Html::checkbox("ProjectCompetitor[%competitor-id%][is_key_competitor]", $projectCompetitor->is_key_competitor, [
                                'id'    => "ProjectCompetitor[%competitor-id%][is_key_competitor]",
                                'class' => 'project_competitor-key'
                            ])?>
                        </label>
                    </td>
                    <td>
                        <?= Html::dropDownList("ProjectCompetitor[%competitor-id%][price_variation_modifier]", $projectCompetitor->price_variation_modifier, [
                            '0%','1%','2%','3%','4%','5%','6%','7%','8%','9%','10%'
                        ], [
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
                        <?=Html::dropDownList("ProjectCompetitor[%competitor-id%][price_final_modifier]",(string)$projectCompetitor->price_final_modifier,$ll, [
                            'class' => 'project_competitor-price_final_modifier',
                            'prompt'=>'Выкл.',
                        ]);?>
                    </td>
                    <td>
                        <?php
                            echo Html::a('<i class="fa fa-play-circle-o" ></i>', [
                                '/parsing-project/execute',
                                'id'            => null,
                                'regions'       => $model->region_id,
                                'projects'      => $model->id,
                                'sources'       => Source::SOURCE_WEBSITE,
                                'priority'      => 1,
                            ], [
                                'class'     => "btn btn-info btn-sm project_competitor-go-parse",
                            ]);
                        ?>
                    </td>
                    <td>
                        <?=Html::a('<span class="glyphicon glyphicon-edit"></span> Бренды', '#',[
                            'class'                 => 'btn btn-default btn-sm brandsShow',
                            'data-competitor_id'    => '%competitor-id%'
                        ])?>
                        <?=Html::hiddenInput("ProjectCompetitorBrands[%competitor-id%][brandsSelected]", '', [
                            'class' => 'project_competitor-brands_selected'
                        ]);?>
                        <?=Html::hiddenInput("ProjectCompetitorBrands[%competitor-id%][brandsBanned]", '', [
                            'class' => 'project_competitor-brands_banned'
                        ]);?>
                    </td>
                    <td>
                        <?=Html::a('<span class="glyphicon glyphicon-edit"></span>  Категории', '#',[
                            'class'                 => 'btn btn-default btn-sm categoriesShow',
                            'data-competitor_id'    => '%competitor-id%'
                        ])?>
                        <?=Html::hiddenInput("ProjectCompetitorCategories[%competitor-id%][categoriesSelected]", '', [
                            'class' => 'project_competitor-categories_selected'
                        ]);?>
                        <?=Html::hiddenInput("ProjectCompetitorCategories[%competitor-id%][categoriesBanned]", '', [
                            'class' => 'project_competitor-categories_banned'
                        ]);?>
                    </td>
                    <td>
                        <?= Html::a('<span class="glyphicon glyphicon-edit"></span>  Исключенные товары (<span class="items-count">0</span>)', '#', [
                            'class'                 => 'btn btn-default itemsShow',
                            'data-competitor_id'    => '%competitor-id%'
                        ]); ?>
                        <?=Html::hiddenInput("ProjectCompetitorItems[%competitor-id%][itemsSelected]", '', [
                            'class' => 'project_competitor-items_selected'
                        ]);?>
                    </td>
                    <td>
                        <?= Html::checkbox("ProjectCompetitor[%competitor-id%][status_id]", $projectCompetitor->status_id != Status::STATUS_ACTIVE, [
                            'id'    => "ProjectCompetitor[%competitor-id%][status_id]",
                            'class' => 'project_competitor-status',
                            'value' => 2
                        ])?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
