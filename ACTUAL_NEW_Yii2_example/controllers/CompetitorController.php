<?php

namespace app\controllers;

use app\components\base\Entity;
use app\components\crud\controllers\ActiveController;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\reference\Competitor;
use app\models\register\Task;
use yii;
use yii\helpers\Url;

class CompetitorController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\Competitor";
    public $searchModelClass    = "app\\models\\reference\\Competitor";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update'        => null,
            'view'          => null,
            'delete'        => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return array_merge(parent::verbs(), [
            'view' => ['GET', 'POST', 'PUT', 'PATCH','HEAD'],
        ]);
    }
    
    public function actionUpdate($id = null) {
        $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @return yii\web\Response
     */
    public function actionUpdatePrices() {
        $task = new Task;
        $task->requester_id         = null;
        $task->requester_entity_id  = Entity::CompetitorItem;
        $task->task_function        = 'updatePrices';
        $task->task_type_id         = TaskType::TYPE_COMPETITOR_ITEM_UPDATE_PRICES;
        $task->enqueue(true);
        return $this->redirect(Url::to(['/competitor']));
    }

    public function actionDelete($id) {

        $competitor = Competitor::findOne($id);

        $competitor->delete();

        $response = Yii::$app->getResponse();
        $response->setStatusCode(204);

        $message = Yii::t('app', '<strong>Success!</strong> Competitor has been deleted.');
        $this->setFlash('success', $message);

        $response->getHeaders()->set('Location', Url::toRoute(['/competitor'], true));
    }

    public function actionView($id = null) {


        if ($id) {
            $competitor = Competitor::findOne($id);
            $competitor->scenario = Competitor::SCENARIO_UPDATE;
        } else {
            $competitor = new Competitor;
            $competitor->scenario = Competitor::SCENARIO_CREATE;
            $competitor->loadDefaultValues();
        }

        $request = Yii::$app->getRequest();

        if ($request->isPost && $post =  $request->post()) {

            $competitor->load($post);

            if (($response = $this->validateAjax($competitor)) ) {
                return $response;
            }

            if (!$competitor->validate()) {
                throw new yii\base\InvalidValueException(yii\helpers\Json::encode($competitor->errors));
            }

            $competitor->setupShopDomains($request->post('CompetitorShopDomain'));
            $competitor->setupShopIndexes($request->post('CompetitorShopIndex'));
            $competitor->setupShopNames($request->post('CompetitorShopName'));
            
            
            $competitor->save();

            $this->redirect(['view', 'id' => $competitor->id]);
        }

        $params = [
            'model'         => $competitor
        ];

        return Yii::$app->request->isAjax ? $this->renderAjax('update', $params) : $this->render('update', $params);

    }
}
