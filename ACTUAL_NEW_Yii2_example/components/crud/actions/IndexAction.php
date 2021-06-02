<?php
/**
 * @link http://netis.pl/
 * @copyright Copyright (c) 2015 Netis Sp. z o. o.
 */

namespace  app\components\crud\actions;

use app\components\crud\controllers\ActiveController;
use app\components\base\BaseModel;
use app\components\DataProvider;
use app\models\reference\JournalSettings;
use netis\crud\db\LabelsBehavior;
use yii;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;

class IndexAction extends Action
{
    const SEARCH_QUICK_SEARCH = 1;
    const SEARCH_COLUMN_HEADERS = 2;
    const SEARCH_ADVANCED_FORM = 4;

    /**
     * @var callable a PHP callable that will be called to prepare a data provider that
     * should return a collection of the models. If not set, [[prepareDataProvider()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($action) {
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return an instance of [[ActiveDataProvider]].
     */
    public $prepareDataProvider;
    /**
     * @var bool should a serial column be used
     */
    public $useSerialColumn = false;
    /**
     * @var bool should a checkbox column be used
     */
    public $useCheckboxColumn = false;


    /**
     * @return ActiveDataProvider
     */
    public function run()
    {
        if ($this->checkAccess) {
            // additional authorization conditions are added in getQuery() method
            // using the "authorized" query
            call_user_func($this->checkAccess, 'read');
        }
        $model  = $this->getSearchModel();

        /** @var BaseModel $searchModel */
        $searchModel = $this->getSearchModel();

        if ($this->controller->view->title === null) {
            $this->controller->view->title = $searchModel->getCrudLabel('index');
        }

        $this->controller->view->params['menu'] = $this->controller->getMenu($this->controller->action, $searchModel);
        $this->controller->view->params['breadcrumbs'] = $this->controller->getBreadcrumbs($this->controller->action, $searchModel);
        
        $journalSettings                = JournalSettings::getUserJournalSettings($searchModel->className());
        if ($journalSettings) {
            if (Yii::$app->request->get('sort', null)) {
                $journalSettings->sort_order = Yii::$app->request->get('sort', null);
            }
            if (Yii::$app->request->get('per-page', null)) {
                $journalSettings->per_page = Yii::$app->request->get('per-page', 50);
            }
            $journalSettings->name = $searchModel->getPluralNominativeName();
            $journalSettings->save();
        }

        $dataProvider = new DataProvider([
            'query'         => $searchModel->crudSearch(array_merge($_GET,$_POST)),
            'sort'          => $searchModel->getSort(['defaultOrder' => $journalSettings->sortOrder]),
            'pagination'    => [
                'pageSizeLimit'     => [-1, 0x7FFFFFFF],
                'defaultPageSize'   => $journalSettings->per_page,
            ],
        ]);

        if (Yii::$app->request->get('delete', false)) {
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
            $dataProvider->pagination->pageSize = 1000;
            /** @var BaseModel[] $models */
            $models = $dataProvider->getModels();
            $deletedCount = 0;
            while (!empty($models)) {
                $deleteIds = [];
                $count = count($models);
                foreach ($models as $model) {
                    $deleteIds[] = $model->id;
                    $deletedCount++;
                    if ($count <= 100) {
                        $model->recycle();
                    }
                }
                if ($count > 100) {
                    $searchModel::deleteAll([
                        'id' => $deleteIds
                    ]);
                }
                $dataProvider->pagination->page = $dataProvider->pagination->page + 1;
                $dataProvider->refresh();
                $models = $dataProvider->getModels();
            }
            return ['success' => true, 'count' => $deletedCount];
        }

        return [
            'dataProvider'  => $dataProvider,
            'columns'       => $this->getIndexGridColumns($model, $this->getFields($model, 'grid')),
            'searchModel'   => $searchModel,
            'buttons'       => $this->controller->getIndexButtons(),
        ];
    }

    /**
     * Retrieves grid columns configuration using the modelClass.
     * @param BaseModel $model
     * @param array $fields
     * @return array grid columns
     */
    public function getIndexGridColumns($model, $fields)
    {
        /** @var ActiveController $controller */
        $controller = $this->controller;
        $actionColumn = new ActionColumn();
        $actionColumn->buttonOptions['class'] = 'operation-button';
        $actionButtons = $controller->indexActionButtons($actionColumn);
        $template = "<nobr>";
        foreach ($actionButtons as $name => $button) {
            $template .= "{" . $name . "} ";
        }
        $template .= "</nobr>";

        $extraColumns = [
            '__actions' => [
                'class'         => \app\widgets\GridView\columns\ActionColumn::className(),
                'headerOptions' => ['class' => 'column-action'],
                'controller'    => Yii::$app->crudModelsMap[$model::className()],
                'template'      => $template,
                'buttons'       => $actionButtons
            ],
        ];
        if ($this->useSerialColumn) {
            $extraColumns['__serial'] = [
                'class'         => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'column-serial'],
            ];
        }
        if ($this->useCheckboxColumn) {
            //$classParts = explode('\\', $this->modelClass);
            $extraColumns['__checkbox'] = [
                'class'         => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'column-checkbox'],
                'multiple'      => true,
                //'name'          => end($classParts).'[]',
            ];
        }

        $columns = array_merge($extraColumns, static::getGridColumns($model, $fields));

        $refinedColumns  = [];

        /** @var BaseModel $model */
        $crudIndexColumns = $model->crudIndexColumns();

        if(count($crudIndexColumns) > 0) {
            array_unshift($crudIndexColumns, '__actions');
            foreach ($crudIndexColumns as $name => $column) {
                if (is_numeric($name)) {
                    if (isset($columns[$column])) {
                        $refinedColumns[$column] = $columns[$column];
                    }
                } else {
                    $refinedColumns[$name] = $column;
                }
            }
        } else {
            $refinedColumns = $columns;
        }

        return $refinedColumns;
    }

    /**
     * @inheritdoc
     */
    protected static function getAttributeColumn($model, $field, $format)
    {
        /** @var LabelsBehavior $behavior */
        $behavior = $model->getBehavior('labels');
        if (in_array($field, $behavior->attributes)) {
            return array_merge(
                parent::getAttributeColumn($model, $field, ['crudLink', [], 'view', function ($value) use ($field) {
                    return Html::encode($value->$field);
                }]),
                [
                    'value' => function ($model/*, $key, $index, $column*/) {
                        return $model;
                    },
                ]
            );
        }
        return parent::getAttributeColumn($model, $field, $format);
    }

}
