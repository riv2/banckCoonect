<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;

class RolesController extends ActiveController
{
    public $modelClass          = 'app\models\reference\Role';
    public $searchModelClass    = 'app\models\reference\Role';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'view'=> null,
        ]);
    }

    /**
     * @inheritDoc
     */
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
