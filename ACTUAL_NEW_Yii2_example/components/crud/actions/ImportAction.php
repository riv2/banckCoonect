<?php
namespace app\components\crud\actions;

use app\components\base\BaseModel;
use app\widgets\FileExchangeWidget\FileExchangeWidget;
use yii;
use yii\data\ActiveDataProvider;

class ImportAction extends Action
{

    /**
     * @return ActiveDataProvider
     */
    public function run()
    {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, 'create');
        }

        /** @var BaseModel $model */
        $modelClass = $this->modelClass;
        $model = new $modelClass();

        if (Yii::$app->getRequest()->isPost) {
            if ($response = FileExchangeWidget::processImportRequest($modelClass, Yii::$app->getRequest()->post())){
                //return $response;
            }
            Yii::$app->response->refresh();
        } else {
            $model->loadDefaultValues();
        }

        return [
            'model'     => $model,
            'values'    => Yii::$app->getRequest()->queryParams,
            'exclude'   => explode(',',Yii::$app->getRequest()->get('exclude','')),
        ];
    }

}