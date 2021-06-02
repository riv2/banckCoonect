<?php

use app\models\enum\Source;
use app\models\reference\Brand;
use app\models\reference\Category;
use app\models\reference\Project;
use yii\bootstrap\Tabs;
use yii\bootstrap\Html;
use app\widgets\FormBuilder;
use kartik\form\ActiveForm;
use \app\models\enum\ProjectExecutionStatus;

/** @var $this \netis\crud\web\View */
/** @var Project $model */
/** @var $form yii\widgets\ActiveForm */
/** @var $controller netis\crud\crud\ActiveController */
/** @var $action netis\crud\crud\UpdateAction */
/** @var $view \netis\crud\web\View */
/** @var $errorSummaryModels array models passed to form error summary, defaults to $model */
/** @var $competitors \app\models\reference\Competitor[]  */
/** @var string $tab */

$controller = $this->context;
$action = $controller->action;
$view = $this;

\app\assets\ProjectFormAsset::register($this);

$projectExecutionId = null;
$projectExecutions = $model->getProjectExecutions()
    ->andWhere([
        'project_execution_status_id' => [ProjectExecutionStatus::STATUS_CLOSED, ProjectExecutionStatus::STATUS_CALCULATED, ProjectExecutionStatus::STATUS_EXPORTED]
    ])
    ->orderBy(['number' => SORT_DESC])
    ->limit(30)
    ->all();
if (!$projectExecutionId && count($projectExecutions) > 0) {
    $projectExecutionId = $projectExecutions[0]->id;
}
$projectExecutionList = [];
foreach ($projectExecutions as $i => $projectExecution) {
    $projectExecutionList[$projectExecution->id] = (string)$projectExecution;
}
?>

    <div class="panel">
    <div class="panel-body">
        <?php
            $form = ActiveForm::begin([
                'id'                    => 'form-update-project',
                'validateOnSubmit'      => true,
                'enableAjaxValidation'  => true,
                'enableClientValidation' => true,
                'method' => 'post',
                'options' => [
                    'data-pjax'         => 1,
                    'data-project_id'   => $model->id,
                    'enctype'           => 'multipart/form-data',
                ]
            ])
        ?>

        <div class="row margin-bottom">
            <?php
            $blocked = false;
//            if (!in_array($model->project_execution_status_id, [ProjectExecutionStatus::STATUS_NEW, ProjectExecutionStatus::STATUS_READY, ProjectExecutionStatus::STATUS_EXPORTED, ProjectExecutionStatus::STATUS_CALCULATED]) ) {
//                $blocked = true;
//            }
            ?>
            <div class="col-sm-7">
                <div class="project-on-unblocked" style="<?=$blocked?'display:none;':''?>">
                    <?php
                        echo Html::submitButton(
                            '<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить',
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']
                        );
                        echo " ";
                        if (!$model->isNewRecord) {
                            echo Html::submitButton(
                                '<span class="glyphicon glyphicon-play"></span> Расчитать',
                                [
                                    'class' => 'btn btn-primary',
                                    'name' => 'execute',
                                    'value' => $model->id,
                                ]
                            );
                            echo " ";
                            echo Html::a(
                                '<span class="fa fa-play-circle-o"></span>  Собрать цены',
                                [
                                    '/parsing-project/execute',
                                    'id'        => null,
                                    'regions'   => $model->region_id,
                                    'projects'  => $model->id,
                                    'sources'   => Source::SOURCE_WEBSITE,
                                    'priority'  => 1,
                                ],
                                ['class' => 'btn btn-info']
                            );
                            echo " ";
                            echo Html::a(
                                '<span class="fa fa-play-circle-o"></span>  Собрать цены с ЯМ',
                                [
                                    '/parsing-project/execute',
                                    'id'        => null,
                                    'regions'   => $model->region_id,
                                    'projects'  => $model->id,
                                    'sources'   => Source::SOURCE_YANDEX_MARKET,
                                    'priority'  => 1,
                                ],
                                ['class' => 'btn btn-info']
                            );
                        }
                    ?>
                </div>
            </div>
            <div class="col-sm-2">
                Статус:<br/>
                <?=$model->projectExecutionStatus?>
            </div>
            <?php if (!$model->isNewRecord) { ?>
                <div class="col-sm-3">
                    Создан:&nbsp;&nbsp;&nbsp; <?=$model->created_at->format('d.m.Y H:i:s')?> <br/>
                    Изменен: <?=$model->updated_at->format('d.m.Y H:i:s')?>
                </div>
            <?php } ?>
        </div>



        <?php
            $tabs = [
                [
                    'label' => 'Конкуренты проекта',
                    'options' => ['id' => 'competitors'],
                    'active' => ($tab=='competitors'),
                ],
                [
                    'label' => 'Настройки',
                    'options' => ['id' => 'settings'],
                    'active' => ($tab=='settings'),
                ],
                [
                    'label' => 'Ассортимент проекта ('.$model->getProjectItems()->count().')',
                    'options' => ['id' => 'project-items'],
                    'active' => ($tab=='project-items'),
                ],
            ];
            if (!$model->isNewRecord) {
                if ($projectExecutionId) {
                    $tabs[] = [
                        'label' => 'Цены',
                        'options' => ['id' => 'prices'],
                        'active' => ($tab=='prices'),
                    ];
                    $tabs[] = [
                        'label' => 'Отчеты',
                        'options' => ['id' => 'reports'],
                        'active' => ($tab=='reports'),
                    ];
                }
                $tabs[] = [
                    'label' => 'Графики',
                    'options' => ['id' => 'charts'],
                    'active' => ($tab=='charts'),
                ];
                echo Tabs::widget([
                    'renderTabContent'  => false,
                    'items'             => $tabs,
                    'options'           => ['class' => 'project_settings-tabs']
                ]);
            }
        ?>

        <?php if (!$model->isNewRecord) { ?>
            <div class="tab-content"><div role="tabpanel" class="tab-pane <?=($tab=='settings')?'active':''?>" id="settings">
        <?php }?>


        <?=$this->render('_form_settings',[
            'form'  => $form,
            'model' => $model
        ])?>

        <?php  if (!$model->isNewRecord) { ?>
        </div>

            <?php if ($projectExecutionId) { ?>

                <div role="tabpanel" class="tab-pane <?=$tab=='prices'?'active':''?>" id="prices">
                    <?=$this->render('_form_prices',[
                        'form'                  => $form,
                        'model'                 => $model,
                        'projectExecutionId'    => $projectExecutionId,
                        'projectExecutionList'  => $projectExecutionList
                    ])?>
                </div>

                <div role="tabpanel" class="tab-pane <?=$tab=='reports'?'active':''?>" id="reports">
                    <?=$this->render('_form_reports',[
                        'form'                  => $form,
                        'model'                 => $model,
                        'projectExecutionId'    => $projectExecutionId,
                        'projectExecutionList'  => $projectExecutionList
                    ])?>
                </div>

            <?php } ?>

            <div role="tabpanel" class="tab-pane <?=$tab=='charts'?'active':''?>" id="charts">
                <?=$this->render('_form_charts',[
                    'form'                  => $form,
                    'model'                 => $model,
                    'projectExecutionId'    => $projectExecutionId,
                    'projectExecutionList'  => $projectExecutionList
                ])?>
            </div>

        <div role="tabpanel" class="tab-pane <?=($tab=='project-items'||$tab==null)?'active':''?>" id="project-items">
            <?=$this->render('_form_project-items',[
                'form'                  => $form,
                'model'                 => $model,
                'projectExecutionId'    => $projectExecutionId,
                'projectExecutionList'  => $projectExecutionList
            ])?>
        <?php }  ?>
        <?php if (!$model->isNewRecord) {?>
            </div><div role="tabpanel" class="tab-pane <?=$tab=='competitors'?'active':''?>" id="competitors">
        <?php } else { ?>
            <legend>Конкуренты проекта</legend>
        <?php } ?>
            <?=$this->render('_form_competitors',[
                'form'                  => $form,
                'model'                 => $model,
                'competitors'           => $competitors,
            ])?>

        <?=(!$model->isNewRecord) ? '</div></div>' : '' ?>

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
        <!-- Modal -->
        <div class="modal fade" id="excludedItems" tabindex="-1" role="dialog" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Исключенные товары у <span class="competitorName"></span></h4>
                    </div>
                    <div class="modal-body">
                        <?=FormBuilder::renderSelect2($this, \app\models\reference\Item::className(), 'select_items', null, false) ?>
                        <textarea name="select_items_text" class="form-control"></textarea>
                        <hr>
                        <div class="items-list">
                            <div id="itemsListTemplate" style="display: none">
                                <span class="item-name"></span>
                                <span class="item-rmv">x</span>
                                <input type="hidden" class="item-id"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-success selectOk"><span class="glyphicon glyphicon-check"></span> Выбрать</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // echo $form->field($model, 'status')->dropDownList(\app\models\enum\Status::getEnumList());
        ?>

        <?php
        $form->end();
        ?>
                </div>
            </div>
