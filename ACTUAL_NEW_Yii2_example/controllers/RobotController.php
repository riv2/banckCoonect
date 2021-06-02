<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\reference\Robot;
use app\models\register\Parsing;
use yii;

class RobotController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\Robot";
    public $searchModelClass    = "app\\models\\reference\\Robot";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'view'          => null,
        ]);
    }

    public function actionView($id) {
        $this->redirect(['update', 'id' => $id]);
    }


    public function actionCancelParsing($id) {

        \Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        
        Robot::cancelParsing($id);

        return ['ok' => true];
    }
    
    /**
     * @return array
     */
    public function getIndexButtons() {

        $buttons = [
        ];

        return $buttons;
    }
}