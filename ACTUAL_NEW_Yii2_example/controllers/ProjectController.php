<?php

namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\components\base\Entity;
use app\models\document\ProjectExecution;
use app\models\enum\ProjectExecutionStatus;
use app\models\enum\TaskType;
use app\models\reference\Brand;
use app\models\reference\Category;
use app\models\reference\Competitor;
use app\models\reference\Item;
use app\models\reference\Project;
use app\models\pool\PriceCalculated;
use app\models\register\Task;
use yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class ProjectController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\Project";
    public $searchModelClass    = "app\\models\\search\\Project";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update'        => null,
            'view'          => null,
            'delete'        => null,
            'prices-report' => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return array_merge(parent::verbs(), [
            'view'      => ['GET', 'POST', 'PUT', 'PATCH','HEAD'],
            'delete'    => ['GET', 'POST', 'DELETE'],
        ]);
    }
    
    public function actionUpdate($id = null) {
        $this->redirect(['view', 'id' => $id]);
    }

    public function actionClear($id) {
        
        $project = Project::findOne($id);
        
        if (!$project) {
            throw new yii\web\NotFoundHttpException('Проект не найден');
        }

        $project->clearProjectItems();
        
        $this->redirect(['view', 'id' => $id]);

    }

    public function actionPricesReport($project_execution_id) {

        $dataProvider = new ActiveDataProvider([
            'query' => PriceCalculated::find()->andWhere(['project_execution_id' => $project_execution_id]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->layout = 'iframe';

        return $this->render('_prices.php', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionExportPrices($project_execution_id) {
        $projectExecution = ProjectExecution::findOne($project_execution_id);
        $projectExecution->exportPrices();
        $this->redirect(['view', 'id' => $projectExecution->project_id]);
    }

    public function actionExecute($id) {
        $project = Project::findOne($id);
        $project->prepareProjectExecution();
        $this->redirect(['view', 'id' => $id]);
    }

    public function actionUpdateUrls($id) {
        $task = new Task;
        $task->requester_id         = $id;
        $task->requester_entity_id  = Entity::Project;
        $task->task_function        = 'itemUpdateUrls';
        $task->task_type_id         = TaskType::TYPE_ITEM_UPDATE_URLS;
        $task->enqueue();
        $this->redirect(['view', 'id' => $id]);
    }

    public function actionProjectItemsAsync($id) {

        $project = Project::findOne($id);
        
        $project->scenario = Project::SCENARIO_UPDATE;

        $params = [
            'model'                => $project,
            'groupedProjectItems'  => $project->groupedProjectItems(),
        ];

        return $this->renderPartial('_form_project-items-async', $params);
    }


    public function actionName2Id() {
        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $type = \Yii::$app->request->post('type','brands');
        $names  = \Yii::$app->request->post('names');

        if (empty($names)) {
            return [];
        }

        $class = Brand::className();

        if ($type == 'categories') {
            /** @var Brand $class */
            $class = Category::className();
        }
        $ids = $class::find()->andWhere(['name' => $names])->select('id')->column();
        return $ids;
    }

    public function actionId2Name() {

        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $type = \Yii::$app->request->post('type','brands');
        $ids  = \Yii::$app->request->post('ids');


        if (empty($ids)) {
            return [];
        }
        $class = Brand::className();

        if ($type == 'items') {
            $names = Item::find()
                ->andWhere(['id' => $ids])
                ->select('id,name')
                ->asArray()
                ->all();
            return $names;
        } else if ($type == 'categories') {
            /** @var Brand $class */
            $class = Category::className();
        }
        $names = $class::find()->andWhere(['id' => $ids])->select('name')->column();
        return $names;
    }

    public function actionAutoItems($id) {
        if ($id) return '';
        $project = Project::findOne($id);
        $project->project_execution_status_id = ProjectExecutionStatus::STATUS_QUEUED;
        $project->save();
        $task = new Task;
        $task->requester_id         = $id;
        $task->requester_entity_id  = Entity::Project;
        $task->task_function        = 'projectAutoFill';
        $task->task_type_id         = TaskType::TYPE_PROJECT_AUTO_FILL;
        $task->enqueue();
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete($id) {

        $project = Project::findOne($id);
        
        if ($project->recycle() === false) {
            throw new ServerErrorHttpException('Не удалось удалить по не известной причине');
        }
        
        $response = Yii::$app->getResponse();
        $response->setStatusCode(204);

        $this->setFlash('success', "Проект $project был удалён");

        $response->getHeaders()->set('Location', Url::toRoute(['/project'], true));
    }

    public function actionView($id = null) {
        
        if ($id) {
            $project = Project::findOne($id);
            $project->scenario = Project::SCENARIO_UPDATE;
            Project::addWorkingProject($id, $project->name);
        } else {
            $project = new Project;
            $project->scenario = Project::SCENARIO_CREATE;
            $project->loadDefaultValues();
        }

        $request = Yii::$app->request;

        if ($request->isPost && $post = $request->post()) {

            if (isset($post['Project']['scheduled_daily_time']) && $post['Project']['scheduled_daily_time']) {
                $chunks = explode(':', $post['Project']['scheduled_daily_time']);
                if (count($chunks) == 2) {
                    $post['Project']['scheduled_daily_time'] = $post['Project']['scheduled_daily_time'] . ':00';
                }
            } else {
                $post['Project']['scheduled_daily_time'] = null;
            }

            $project->load($post);

            if (!isset($post['Project']['scheduledWeekdays'])) {
                $project->scheduled_weekdays = null;
            }

            if (($response = $this->validateAjax($project)) ) {
                return $response;
            }

            if (!$project->validate()) {
                throw new yii\base\InvalidValueException(yii\helpers\Json::encode($project->errors));
            }

            $project->save();

            $project->setupGroupedProjectItemsParams($post);

            $project->setupSources($request->post('ProjectSources'));
            
            $project->setupProjectPriceFormerTypes(explode(',', $request->post('ProjectPriceFormerTypes','')));
            
            $project->setupRegions(explode(',', $request->post('Regions','')));

            if ($project->isNewRecord || $request->get('tab', 'competitors') == 'competitors') {
                $project->setupCompetitors($post);
            }

            if (isset($post['execute'])) {
                $project->prepareProjectExecution();
            }
            $url = ['view', 'id' => $project->id];
            if (isset($_GET['tab'])) {
                $url['tab'] = $_GET['tab'];
            }
            $this->redirect($url);
        }

        $competitors = Competitor::find()->orderBy(['name' => SORT_ASC])->all();

        $params = [
            'model'         => $project,
            'competitors'   => $competitors,
            'tab'           => $request->get('tab',$project->isNewRecord?'settings':'competitors')
        ];

        return Yii::$app->request->isAjax ? $this->renderAjax('update', $params) : $this->render('update', $params);

    }

    /**
     * @param $id
     */
    public function actionClone($id) {
        $parsingProject = Project::findOne($id);
        $clone = $parsingProject->cloneProject();
        $this->redirect(['update', 'id' => $clone->id]);
    }


    /**
     * @inheritdoc
     */
    public function indexActionButtons($actionColumn) {
        $controller = $this;
        return array_merge(parent::indexActionButtons($actionColumn), [
            'view' => function(){
                return null;
            },
            'clone' => function ($url, $model, $key) use ($controller, $actionColumn) {
                $icon    = '<i class="fa fa-clone" aria-hidden="true"></i>';
                $options = array_merge([
                    'title'        => 'Клонировать',
                    'aria-label'   => 'Клонировать',
                    'data-pjax'    => '0',
                ],  $actionColumn->buttonOptions);
                return Html::a($icon, $url, $options);
            },
        ]);
    }
}
