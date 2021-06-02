<?php

/**
 * @var $this           netis\crud\web\View
 * @var $dataProvider   yii\data\ActiveDataProvider
 * @var $columns        array
 * @var $searchModel    \app\models\reference\Project
 * @var $controller     \app\components\crud\controllers\ActiveController
 * @var $buttons        array
 */

use app\models\enum\Status;
use app\models\reference\ProjectTheme;
use yii\bootstrap\Tabs;
use yii\bootstrap\Html;
use app\widgets\FormBuilder;

FormBuilder::registerSelect($this);

echo FormBuilder::registerRelations($this);

$dataProvider->pagination->pageSize = 1000;

\app\assets\ProjectIndexAsset::register($this);

/** Интерфейс вкладок Тем проектов 
 * <!-- 
 */
/** @var ProjectTheme[] $projectThemes */
$projectThemes = ProjectTheme::find()->select(['id','name'])->andWhere(['status_id' => Status::STATUS_ACTIVE])->asArray()->all();
$tabs = [
    [
        'label'     => 'Все проекты',
        'options'   => ['id' => ''],
    ],
    [
        'label'     => 'Без тематики',
        'options'   => ['id' => 'null'],
    ]
];
foreach ($projectThemes as $projectTheme) {
    $tabs[] = [
        'label'     => $projectTheme['name'],
        'options'   => [
            'id'    => $projectTheme['id'],
        ],
        'active'    => ($searchModel->project_theme_id == $projectTheme['id']),
    ];
}
/** 
 * --!> 
 */

echo Html::beginTag('div',[
    'class' => 'm-b-5 text-right'
]);
echo Html::a('<span class="glyphicon glyphicon-plus"></span> Создать', ['update'], [
    'class' => 'btn btn-success'
]);
echo Html::endTag('div');

echo Tabs::widget([
    'renderTabContent'  => false,
    'items'             => $tabs,
    'options'           => ['class' => 'project_theme-tabs']
]);

echo $this->render('_grid', [
    'gridId'        => 'projectIndexGrid',
    'gridOptions'   => array_merge([
        'showSummary'    => false,
        'showPager'      => false,
        'showButtons'    => false,
        'buttons'        => $buttons,
    ]),
    'columns'       => $columns,
    'dataProvider'  => $dataProvider,
    'searchModel'   => $searchModel,
], $this->context);