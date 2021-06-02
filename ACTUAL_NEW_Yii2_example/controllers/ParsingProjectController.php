<?php
namespace app\controllers;

use app\components\base\BaseModel;
use app\components\base\Entity;
use app\components\crud\controllers\ActiveController;
use app\models\cross\ParsingProjectMasks;
use app\models\cross\ParsingProjectProject;
use app\models\cross\ParsingProjectRegion;
use app\models\enum\Region;
use app\models\enum\Status;
use app\models\enum\TaskType;
use app\models\reference\ParsingProject;
use app\models\reference\ProjectCompetitor;
use app\models\reference\Schedule;
use app\models\register\Task;
use netis\crud\crud\Action;
use yii;
use yii\helpers\Html;

class ParsingProjectController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\ParsingProject";
    public $searchModelClass    = "app\\models\\reference\\ParsingProject";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update'        => null,
            'view'          => null,
            'execute'       => null,
        ]);
    }

    public function hasAccess($action, $model = null, $params = [])
    {
        return parent::hasAccess($action, $model, $params);
    }

    public function actionExecute($id = null, $test = false) {
        if (!$id) {
            $find = ParsingProject::find()->alias('pp')->select('pp.id')->andWhere([
                'pp.status_id' => Status::STATUS_ACTIVE
            ]);


            // только проекты парсинга которые содержат конкурентов проектов расчета
            if (Yii::$app->request->get('projects', null)) {
                $find = $find->andWhere([
                    'pp.competitor_id' => ProjectCompetitor::find()->select('competitor_id')->andWhere([
                        'project_id' =>  explode(',', Yii::$app->request->get('projects'))
                    ])->column()
                ]);
            }

            // только проекты парсинга указанного конкурента
            if (Yii::$app->request->get('competitors', null)) {
                $find = $find->andWhere([
                    'pp.competitor_id' => explode(',', Yii::$app->request->get('competitors'))
                ]);
            }
            // только проекты парсинга в которых есть указанные регионы
            if (Yii::$app->request->get('regions', null)) {
                $find = $find->innerJoin(['ppr' => ParsingProjectRegion::tableName()],'ppr.parsing_project_id = pp.id')->andWhere([
                    'ppr.region_id' => explode(',', Yii::$app->request->get('regions'))
                ]);
            }

            // только проекты парсинга определенного источника
            if (Yii::$app->request->get('sources', null)) {
                $find = $find->andWhere([
                    'pp.source_id' => explode(',', Yii::$app->request->get('sources'))
                ]);
            }

            // только проекты парсинга цен
            $find->andWhere([
                'pp.parsing_type' => 'normal'
            ]);

            // только проекты парсинга которые есть в расписании
//            $find->innerJoin(
//                ['sh' => Schedule::tableName()],'sh.requester_id = pp.id AND sh.requester_entity_id='.Entity::ParsingProject
//            );

            // только проекты парсинга которые используются для сбора для проектов расчета
            $find->andWhere([
                'pp.used_by_calc' => true
            ]);

            $id = $find->column();

            if (!$id) {
                return "Нет подходящего проекта парсинга ";
            }


        }

        /** @var ParsingProject[] $parsingProjects */
        $parsingProjects = ParsingProject::find()
            ->andWhere([
                'id' => $id,
                'status_id' => Status::STATUS_ACTIVE
            ])
            ->all();

        $lastId = null;
        if ($parsingProjects) {
            foreach ($parsingProjects as $parsingProject) {
                $task = new Task;
                $task->requester_id         = $parsingProject['id'];
                $task->requester_entity_id  = Entity::ParsingProject;
                $task->task_function        = 'startParsing';
                $task->task_type_id         = TaskType::TYPE_START_PARSING;
                $task->setParams(Yii::$app->request->queryParams);
                $task->enqueue();
            }
        }

        //return $this->redirect(['/']);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUrls($id = null) {
        $parsingProject = ParsingProject::findOne($id);
        $items = $parsingProject->execute([], true);
        return $this->render('urls-preview', [
            'items'             => $items,
            'parsingProject'    => $parsingProject
        ]);
    }

    public function actionUpdate($id = null) {

        /** @var ParsingProject $parsingProject */
        if (!$id) {
            $parsingProject = new ParsingProject;
            $parsingProject->scenario = ParsingProject::SCENARIO_UPDATE;
            $parsingProject->loadDefaultValues();
        } else {
            $parsingProject = ParsingProject::findOne($id);
            $parsingProject->scenario = ParsingProject::SCENARIO_UPDATE;
        }

        $request = Yii::$app->request;

        if ($request->isPost && $post = $request->post()) {

            $parsingProject->load($post);

            if (($response = $this->validateAjax($parsingProject))) {
                return $response;
            }

            if (!$parsingProject->validate()) {
                throw new yii\base\InvalidValueException(yii\helpers\Json::encode($parsingProject->errors));
            }

            if ($parsingProject->isNewRecord) {
                $parsingProject->save();
            }

            $parsingProjectRegions = $parsingProject->getParsingProjectRegions()
                ->select([
                    'region' => 'cast(region_id as varchar(255))',
                    'cookies',
                    'url_replace_from' => '(CASE WHEN url_replace_from IS NOT NULL THEN url_replace_from ELSE \'\' END)',
                    'url_replace_to' => '(CASE WHEN url_replace_to IS NOT NULL THEN url_replace_to ELSE \'\' END)',
                ])
                ->asArray()
                ->all();
            $requestRegions = $request->post('ParsingProjectRegion', []);
            if($parsingProject->is_our_regions) {
                $requestRegions = array_merge(
                    $requestRegions,
                    array_map(function($a) {
                        return [
                            'region' => (string)$a,
                            'cookies' => '',
                            'url_replace_from' => '',
                            'url_replace_to' => '',
                        ];
                    }, Region::ourRegions()));
            }
            $regionsToDelete = [];
            $regionsToAdd = [];
            foreach ($parsingProjectRegions as $region) {
                foreach ($requestRegions as $requestRegion) {
                    if ($region['region'] === $requestRegion['region']) {
                        if (array_diff_assoc($region, $requestRegion)) {
                            $regionsToDelete[] = $region;
                            $regionsToAdd[] = $requestRegion;
                        }
                        continue 2;
                    }
                }
                $regionsToDelete[] = $region;
            }
            $regionsIds = array_map(function($a) {return $a['region'];}, $parsingProjectRegions);
            foreach ($requestRegions as $requestRegion) {
                if (!in_array($requestRegion['region'], $regionsIds)) {
                    $regionsToAdd[] = $requestRegion;
                }
            }

            ParsingProjectRegion::deleteAll([
                'parsing_project_id' => $parsingProject->id,
                'region_id' => array_map(function($a) {return $a['region'];}, ($regionsToDelete)),
            ]);

            $ri = 0;
            foreach ($regionsToAdd as $region) {
                $parsingProjectRegion = new ParsingProjectRegion;
                $parsingProjectRegion->parsing_project_id   = $parsingProject->id;
                $parsingProjectRegion->region_id            = $region['region'];
                $parsingProjectRegion->sort                 = $ri;
                $parsingProjectRegion->cookies              = $region['cookies'];
                $parsingProjectRegion->url_replace_from     = $region['url_replace_from'];
                $parsingProjectRegion->url_replace_to       = $region['url_replace_to'];
                $parsingProjectRegion->save();
                $ri++;
            }

            ParsingProjectProject::deleteAll([
                'parsing_project_id' => $parsingProject->id
            ]);

            if ($projects = $request->post('ParsingProjectProject',[])) {
                if($projects) {
                    $projects = is_array($projects) ? $projects : explode(',', $projects);
                    foreach ($projects as $projectId) {
                        $parsingProjectRegion = new ParsingProjectProject;
                        $parsingProjectRegion->parsing_project_id = $parsingProject->id;
                        $parsingProjectRegion->project_id = $projectId;
                        $parsingProjectRegion->save();
                    }
                }
            }

            ParsingProjectMasks::deleteAll([
                'parsing_project_id' => $parsingProject->id
            ]);

            if ($masks = $request->post('ParsingProjectMasks',[])) {
                if($masks) {
                    $masks = is_array($masks) ? $masks : explode(',',$masks);
                    foreach ($masks as $masksId) {
                        $parsingProjectMasks = new ParsingProjectMasks;
                        $parsingProjectMasks->parsing_project_id = $parsingProject->id;
                        $parsingProjectMasks->masks_id = $masksId;
                        $parsingProjectMasks->save();
                    }
                }
            }

            $parsingProject->save();

            $url = ['update', 'id' => $parsingProject->id];

            $this->redirect($url);
        }

        $params = [
            'model'         => $parsingProject,
        ];

        return Yii::$app->request->isAjax ? $this->renderAjax('update', $params) : $this->render('update', $params);
    }

    public function actionView($id) {
        $this->redirect(['update', 'id' => $id]);
    }

//    public function actionClone($id) {
//        $parsingProject = ParsingProject::findOne($id);
//        $clone = $parsingProject->cloneParsingProject();
//        $this->redirect(['update', 'id' => $clone->id]);
//    }

    /**
     * @inheritdoc
     */
    public function indexActionButtons($actionColumn) {
        $controller = $this;
        return array_merge(parent::indexActionButtons($actionColumn),[
            'view' => function(){
                return null;
            },
            'execute' => function ($url, $model, $key) use ($controller, $actionColumn) {
                return Html::a('<i class="fa fa-play"></i> Запустить', ['/parsing-project/execute','id' => $model->id],['data-pjax' => 0]);
            }

//            'clone' => function ($url, $model, $key) use ($controller, $actionColumn) {
//                /** @var BaseModel $model */
//                $icon    = '<i class="fa fa-clone" aria-hidden="true"></i>';
//                $options = array_merge([
//                    'title'        => 'Клонировать',
//                    'aria-label'   => 'Клонировать',
//                    'data-pjax'    => '0',
//                ],  $actionColumn->buttonOptions);
//                return Html::a($icon, $url, $options);
//            },
        ]);
    }

    /**
     * @return array
     */
    public function getIndexButtons() {

        $buttons = parent::getIndexButtons();

        return $buttons;
    }
}