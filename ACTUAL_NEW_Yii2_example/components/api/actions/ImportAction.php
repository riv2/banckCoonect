<?php
namespace app\components\api\actions;

use app\components\base\BaseModel;
use yii;
use yii\base\InvalidValueException;
use yii\web\Response;

class ImportAction extends \yii\rest\Action
{
    public $modelClass;

    public function beforeRun()
    {
        $this->controller->enableCsrfValidation = false;
        return parent::beforeRun();
    }

    public function run() {

        $modelClass = $this->modelClass;

        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var BaseModel $model */
        $model = new $modelClass;

        $model->loadDefaultValues();

        $request = array_merge(Yii::$app->getRequest()->get(), Yii::$app->getRequest()->post());

        try {
            $model->importOneFromFile($request);
            Yii::$app->response->content = '{"success":true}';
        } catch (InvalidValueException $e) {
            Yii::$app->response->content = '{"validate_errors":'.$e->getMessage().'}';
        }
    }
}