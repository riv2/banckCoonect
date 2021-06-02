<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\reference\Robot;
use yii;

class UsersController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\User";
    public $searchModelClass    = "app\\models\\reference\\User";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'view'=> null,
        ]);
    }

    public function actionView($id) {
        $this->redirect(['update', 'id' => $id]);
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