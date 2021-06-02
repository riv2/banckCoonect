<?php

namespace app\controllers;

use app\components\base\Entity;
use app\components\crud\controllers\ActiveController;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\register\Task;
use yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class CompetitorItemController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\CompetitorItem";
    public $searchModelClass    = "app\\models\\reference\\CompetitorItem";

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
        return $this->redirect(Url::to(['/competitor-item']));
    }

    /**
     * @return array
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'delete' => null,
        ]);
    }

    /**
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /** @var CompetitorItem $model */
        $model = ($this->modelClass)::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Object not found: $id");
        }
        $model->status_id = Status::STATUS_REMOVED;
        $model->save();

        $response = Yii::$app->getResponse();
        $response->setStatusCode(204);
        $response->getHeaders()->set('Location', Yii::$app->request->referrer ?: Url::toRoute(['index'], true));
    }
}
