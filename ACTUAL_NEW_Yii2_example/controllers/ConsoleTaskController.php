<?php

namespace app\controllers;

use app\components\base\Entity;
use app\components\crud\controllers\ActiveController;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\ConsoleTask;
use app\models\register\Task;
use yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ConsoleTaskController extends ActiveController
{
    public $modelClass          = 'app\models\reference\ConsoleTask';
    public $searchModelClass    = 'app\models\reference\ConsoleTask';
}
