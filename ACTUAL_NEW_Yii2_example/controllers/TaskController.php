<?php

namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\register\FileExchange;
use app\models\register\Task;
use PhpParser\Node\Scalar\MagicConst\File;
use yii;

class TaskController extends ActiveController
{
    public $modelClass          = "app\\models\\register\\Task";
    public $searchModelClass    = "app\\models\\register\\Task";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'cancel'        => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return array_merge(parent::verbs(), [
            'cancel' => ['GET', 'POST', 'DELETE'],
        ]);
    }

    /**
     * Отмена задачи
     * @param null $id
     * @return null
     */
    public function actionCancel($id = null) {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        /** @var Task $task */
        $task = Task::findOne($id);
        if (!$task) {
            throw new yii\base\InvalidValueException("Нет такой задачи");
        }
        $task->cancel();
        return null;
    }

    /**
     * Отмена экспорта/импорта файлов
     * @param null $id
     * @return null
     */
    public function actionCancelFileExchange($id = null) {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        /** @var FileExchange $task */
        $task = FileExchange::findOne($id);
        if (!$task) {
            throw new yii\base\InvalidValueException("Нет такой задачи");
        }
        $task->cancel();
        return null;
    }
}
