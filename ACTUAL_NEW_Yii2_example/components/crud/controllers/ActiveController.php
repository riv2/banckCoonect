<?php
namespace app\components\crud\controllers;
use app\components\crud\actions\Action;
use app\components\base\BaseModel;
use netis\crud\db\ActiveSearchInterface;
use yii;
use yii\helpers\Html;
use yii\web\Response;


/**
 * Class ActiveController
 * @package app\components\crud\controllers
 *
 * @property Action action
 */
class ActiveController extends \netis\crud\crud\ActiveController
{
    /**
     * @var string the scenario used for creating a model.
     * @see ActiveRecord::scenarios()
     */
    public $createScenario = BaseModel::SCENARIO_CREATE;
    /**
     * @var string the scenario used for updating a model.
     * @see ActiveRecord::scenarios()
     */
    public $updateScenario = BaseModel::SCENARIO_UPDATE;

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = [
            'index' => ['GET', 'HEAD', 'POST'],
            'view' => ['GET', 'HEAD'],
            'create' => ['GET', 'POST'], // added GET, which returns an empty model
            'update' => ['GET', 'POST', 'PUT', 'PATCH'], // added GET and POST for compatibility
            'delete' => ['GET', 'POST', 'DELETE'], // added POST for compatibility
        ];
        foreach ($this->actionsClassMap as $id => $action) {
            if (is_array($action) && isset($action['verbs'])) {
                $verbs[$id] = $action['verbs'];
            }
            if (!isset($verbs[$id])) {
                $verbs[$id] = ['GET'];
            }
        }
        return $verbs;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => \app\components\crud\actions\IndexAction::className(),
            ],
            'view' => [
                'class' => \app\components\crud\actions\ViewAction::className(),
            ],
            'update' => [
                'class' => \app\components\crud\actions\UpdateAction::className(),
                'createScenario' => $this->createScenario,
                'updateScenario' => $this->updateScenario,
            ],
            'import' => [
                'class' => \app\components\crud\actions\ImportAction::className(),
            ],
            'export' => [
                'class' => \app\components\crud\actions\ExportAction::className(),
            ],
            'delete' => [
                'class' => \app\components\crud\actions\DeleteAction::className(),
            ],
        ]);
    }

    public function beforeAction($action)
    {
        if (YII_ENV_DEV || YII_DEBUG) {
            Yii::$app->assetManager->forceCopy = true;
        }
        if (Yii::$app->request->get('iframe',null)) {
            $this->layout = 'iframe';
        }
        return parent::beforeAction($action); 
    }


    /**
     * Calls ActiveForm::validate() on the model if current request is ajax and not pjax.
     * @param \app\components\base\BaseModel|array $model
     * @return Response returns boolean false if current request is not ajax or is pjax
     */
    protected function validateAjax($model)
    {
        if (!Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
            return false;
        }
        $response = clone Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        if (!is_array($model)) {
            $model = [$model];
        }
        $response->content = json_encode(call_user_func_array('\yii\widgets\ActiveForm::validate', $model));
        return $response;
    }

    public function setFlash($key, $value = true, $removeAfterAccess = true)
    {
        if (Yii::$app->response->format === Response::FORMAT_HTML) {
            Yii::$app->session->setFlash($key, $value, $removeAfterAccess);
        }
    }

    /**
     * Кнопки в колонке действий Index'а
     * @param $actionColumn
     * @return array
     */
    public function indexActionButtons($actionColumn) {
        $controller = $this;
        return [
            'view'   => function ($url, $model, $key) use ($controller, $actionColumn) {
                if (!$controller->hasAccess('read', $model)) {
                    return null;
                }

                return $actionColumn->buttons['view']($url, $model, $key);
            },
            'update' => function ($url, $model, $key) use ($controller, $actionColumn) {
                if (!$controller->hasAccess('update', $model)) {
                    return null;
                }

                return $actionColumn->buttons['update']($url, $model, $key);
            },
            'delete' => function ($url, $model, $key) use ($controller, $actionColumn) {
                /** @var BaseModel $model  */
                if (!$this->hasAccess('delete', $model) || !$model::crudDeleteEnabled()) {
                    return null;
                }

                return $actionColumn->buttons['delete']($url, $model, $key);
            },
            'toggle' => function ($url, $model, $key) use ($controller, $actionColumn) {
                /** @var BaseModel $model */
                if ($model->getBehavior('toggable') === null || !$controller->hasAccess('delete', $model)) {
                    return null;
                }
                $enabled = false;
                if (method_exists($model, 'isEnabled')) {
                    $enabled = $model->isEnabled();
                }
                $icon    = '<span class="glyphicon glyphicon-'.($enabled ? 'ban' : 'reply').'"></span>';
                $options = array_merge([
                    'title'       => $enabled ? Yii::t('app', 'Disable') : Yii::t('app', 'Enable'),
                    'aria-label'  => $enabled ? Yii::t('app', 'Disable') : Yii::t('app', 'Enable'),
                    'data-pjax'   => '0',
                ], $enabled ? [
                    'data-confirm' => Yii::t('app', 'Are you sure you want to disable this item?'),
                ] : [], $actionColumn->buttonOptions);
                return Html::a($icon, $url, $options);
            },
        ];
    }

    /**
     * @return ActiveSearchInterface
     */
    public function getSearchModel()
    {
        $modelClass = parent::getSearchModel();
        $modelClass->scenario = BaseModel::SCENARIO_SEARCH;
        return $modelClass;
    }

    /**
     * @return array
     */
    public function getIndexButtons() {

        $buttons = [];

        /** @var BaseModel $searchModel */
        $searchModel = $this->getSearchModel();

        if ($this->layout != 'iframe') {
            if ($searchModel::crudDeleteEnabled()) {
                $buttons[] = [
                    'label' => '<span class="glyphicon glyphicon-remove"></span> Удалить',
                    'url' => array_merge(['index'], [$searchModel->formName() => Yii::$app->request->get($searchModel->formName())]),
                    'options' => [
                        'data-pjax' => 1,
                        'disabled' => empty($searchModel->getAppliedSearchFilters()),
                        'class' => 'btn btn-danger delete-found',
                    ]
                ];
            }
            if ($searchModel::crudCreateEnabled()) {
                $buttons[] = [
                    'label' => '<span class="glyphicon glyphicon-plus"></span> Создать',
                    'url' => ['update'],
                    'options' => [
                        'data-pjax' => 0,
                        'class' => 'btn btn-success',
                        'target' => '_blank',
                    ]
                ];
            }
        }
        if ($searchModel::fileImportEnabled()) {
            $buttons[] = [
                'label' => '<span class="glyphicon glyphicon-import"></span> Импорт',
                'url' => ['import'],
                'options' => [
                    'data-pjax' => 0,
                    'class' => 'btn btn-info',
                    'target' => '_blank',
                ]
            ];
        }
        if ($searchModel::fileExportEnabled()) {
            $buttons[] = [
                'label' => '<span class="glyphicon glyphicon-export"></span> Экспорт',
                'url' => array_merge(
                    ['export'],
                    Yii::$app->request->get()
                ),
                'options' => [
                    'data-pjax' => 0,
                    'class' => 'btn btn-info',
                    'target' => '_blank',
                ]
            ];
        }
        
        return $buttons;
    }
}