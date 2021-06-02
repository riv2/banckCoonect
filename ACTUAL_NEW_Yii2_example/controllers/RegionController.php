<?php

namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\enum\Region;
use app\models\register\FileExchange;
use app\models\register\Task;
use PhpParser\Node\Scalar\MagicConst\File;
use yii;

class RegionController extends ActiveController
{
    public $modelClass          = "app\\models\\enum\\Region";
    public $searchModelClass    = "app\\models\\enum\\Region";

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
    public function actionUpdateAjax($id) {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        /** @var Region $region */
        $region = Region::findOne($id);
        if (!$region) {
            throw new yii\base\InvalidValueException("Нет такой задачи");
        }
        $region->setAttributes(Yii::$app->request->post());
        if ($region->validate()) {
            $region->save();
            return true;
        } else {
            return $region->errors;
        }
    }

}
