<?php

namespace app\controllers;


use app\components\DataProvider;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\Status;
use app\models\reference\Brand;
use app\models\reference\Competitor;
use app\models\reference\Project;
use app\models\reference\ProjectCompetitor;
use app\models\reference\ProjectCompetitorBrand;
use app\models\reference\ProjectCompetitorCategory;
use app\models\register\HoradricCube;
use yii\web\Response;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class ProjectCompetitorController extends Controller
{

    public function actionIndex($competitorIds = null , $projectIds = null) {

        // Для вывода в селекты
        $projects   = $this->loadProjects();
        $competitors= $this->loadCompetitors();
        // ---------------------------

        $pcs = ProjectCompetitor::find()->andWhere([
            ProjectCompetitor::tableName().'.status_id' => [Status::STATUS_ACTIVE, Status::STATUS_DISABLED]
        ]);

        $hasFilter = false;
        if ($competitorIds) {
            $hasFilter = true;
            $pcs->andFilterWhere([ ProjectCompetitor::tableName().'.competitor_id' => explode(',', $competitorIds)]);
        }
        if ($projectIds) {
            $hasFilter = true;
            $pcs->andFilterWhere([ ProjectCompetitor::tableName().'.project_id' => explode(',', $projectIds)]);
        }

        if(!$hasFilter) {
            return $this->render('index',[
                'competitorIds' => $competitorIds,
                'projectIds'    => $projectIds,
                'competitors'   => $competitors,
                'projects'      => $projects,
                'dataProvider'  => null,
                'models'        => [],
            ]);
        }

        $pcs->joinWith('competitor')
            ->andWhere([
                ProjectCompetitor::tableName().'.project_id' => ArrayHelper::getColumn($projects,'id'),
                ProjectCompetitor::tableName().'.competitor_id' => ArrayHelper::getColumn($competitors,'id'),
            ])
            ->orderBy([
                Competitor::tableName().'.name' => SORT_ASC,
                ProjectCompetitor::tableName().'.project_id' => SORT_ASC,
            ]);

        // Для пагинации без строк projectCompetitorCategories и projectCompetitorBrands
        $prcPagination = clone $pcs;

        $dataProvider = new DataProvider([
            'pagination' => [
                'pageSize' => 300,
            ],
            'query' => $prcPagination,
        ]);
        // ------------------------------

        $models = $pcs->andWhere([
                ProjectCompetitor::tableName().'.id' => ArrayHelper::getColumn($dataProvider->getModels(),'id')
            ])
            ->joinWith('projectCompetitorCategories')
            ->joinWith('projectCompetitorBrands')
            ->asArray()
            ->all();

        // Потому что нельзя приджойнить цитусовые project к нецитусовой project-competitor
        foreach ($models as $i => $model) {
            foreach ($projects as $project) {
                if ($project['id'] === $model['project_id']){
                    $models[$i]['projectName'] = $project['text'];
                    break;
                }
            }
        }
        // ---------------------

        return $this->render('index',[
            'competitorIds' => $competitorIds,
            'projectIds'    => $projectIds,
            'competitors'   => $competitors,
            'projects'      => $projects,
            'dataProvider'  => $dataProvider,
            'models'        => $models,
        ]);
    }

    public function actionUpdate($id){
        Yii::$app->response->format =  Response::FORMAT_JSON;
        $projectCompetitor = ProjectCompetitor::findOne($id);
        if ($projectCompetitor) {
            ProjectCompetitorBrand::deleteAll([
                'project_competitor_id' => $id,
            ]);
            ProjectCompetitorCategory::deleteAll([
                'project_competitor_id' => $id,
            ]);
            $projectCompetitor->setAttributes(Yii::$app->request->post());
            $projectCompetitor->save();
        }
        return ['success' => 1, 'data' => $projectCompetitor->getAttributes()];
    }

    /**
     * @return array
     */
    private function loadProjects() {
        $projects = Yii::$app->cache->get('project-competitors--projects');
        if (!$projects) {
            $projects = Project::find()
                ->andWhere([
                    'status_id' => Status::STATUS_ACTIVE
                ])
                ->orderBy(['name' => SORT_ASC])
                ->select(['id','text' => 'name'])
                ->asArray()
                ->all();
            Yii::$app->cache->set('project-competitors--projects', $projects, 180);
        }
        return $projects;
    }

    /**
     * @return array
     */
    private function loadCompetitors() {
        $competitors = Yii::$app->cache->get('project-competitors--competitors');
        if (!$competitors) {
            $competitors = Competitor::find()
                ->andWhere([
                    'status_id' => Status::STATUS_ACTIVE
                ])
                ->orderBy(['name' => SORT_ASC])
                ->select(['id', 'text' => 'name'])
                ->asArray()
                ->all();

            Yii::$app->cache->set('project-competitors--competitors', $competitors, 180);
        }
        return $competitors;
    }
}
