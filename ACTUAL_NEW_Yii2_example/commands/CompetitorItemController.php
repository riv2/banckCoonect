<?php
namespace app\commands;

use app\components\base\Entity;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\reference\CompetitorItem;
use app\models\register\Task;
use app\processing\CompetitorItemProcessing;
use yii\console\Controller;

class CompetitorItemController extends Controller
{
    /**
     * Актуализация цен и урлов у конкурентов
     */
    public function actionUpdatePrices() {
        $task = new Task;
        $task->requester_id         = null;
        $task->requester_entity_id  = Entity::CompetitorItem;
        $task->task_function        = 'updatePrices';
        $task->task_status_id       = TaskStatus::STATUS_RUNNING;
        $task->task_type_id         = TaskType::TYPE_COMPETITOR_ITEM_UPDATE_PRICES;
        $task->enqueue(true);
    }

    /**
     *
     */
    public function actionErrors() {
        $task = new Task;
        $task->requester_entity_id  = Entity::CompetitorItem;
        $task->task_function        = 'updateErrors';
        $task->task_type_id         = TaskType::TYPE_COMPETITOR_ITEM_ERRORS;
        $task->enqueue(true);
    }
    /**
     *
     */
    public function actionErrorsTest() {
        CompetitorItemProcessing::updateErrors(null);
    }
}