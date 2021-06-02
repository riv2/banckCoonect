<?php
namespace app\components\crud\actions;

use app\components\base\BaseModel;
use app\widgets\FileExchangeWidget\FileExchangeWidget;
use yii;
use yii\data\ActiveDataProvider;

class ExportAction extends Action
{

    /**
     * @return ActiveDataProvider
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, 'read');
        }

        /** @var BaseModel $model */
        $modelClass = $this->modelClass;
        $model = new $modelClass();

        if (Yii::$app->getRequest()->isPost) {
            if ($filePath = FileExchangeWidget::processExportRequest($modelClass, Yii::$app->getRequest()->post())){

            }
            Yii::$app->response->refresh();
        } else {
            $model->loadDefaultValues();
        }

        return [
            'model'     => $model,
            'values'    => Yii::$app->getRequest()->queryParams,
        ];
    }

}