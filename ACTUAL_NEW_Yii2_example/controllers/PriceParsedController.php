<?php
namespace app\controllers;

use app\components\base\BaseModel;
use app\components\crud\controllers\ActiveController;
use netis\crud\db\ActiveSearchInterface;
use yii;
use yii\helpers\Html;
use yii\helpers\Json;

class PriceParsedController extends ActiveController
{
    public $modelClass          = "app\\models\\pool\\PriceParsed";
    public $searchModelClass    = "app\\models\\pool\\PriceParsed";

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
     * @return array
     */
    public function getIndexButtons() {

        $buttons = [
        ];

        return $buttons;
    }

    /**
     * @return ActiveSearchInterface
     */
    public function getSearchModel()
    {
        /** @var BaseModel $modelClass */
        $modelClass = parent::getSearchModel();
        $modelClass->scenario = 'matching';
        return $modelClass;
    }


    /**
     * Кнопки в колонке действий Index'а
     * @param $actionColumn
     * @return array
     */
    public function indexActionButtons($actionColumn) {
        $controller = $this;
        return [
            'view'   => function ($url, $model, $key) use ($controller, $actionColumn) {
                return null;
            },
            'update' => function ($url, $model, $key) use ($controller, $actionColumn) {
                return null;
            },
            'delete' => function ($url, $model, $key) use ($controller, $actionColumn) {
                return null;
            },
        ];
    }
}