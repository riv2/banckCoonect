<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\reference\Masks;
use app\models\reference\ParsingProject;
use app\models\reference\Robot;
use yii;
use yii\helpers\Html;

class MasksController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\Masks";
    public $searchModelClass    = "app\\models\\reference\\Masks";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update'        => null,
            'view'          => null,
        ]);
    }

    /**
     * Кнопки в колонке действий Index'а
     * @param $actionColumn
     * @return array
     */
    public function indexActionButtons($actionColumn) {
        return array_merge([
            'test'          => null,
            'update'        => null,
            'view'          => null,
        ],parent::indexActionButtons($actionColumn));
    }
}