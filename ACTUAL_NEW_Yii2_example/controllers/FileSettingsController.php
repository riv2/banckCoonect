<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\reference\FileProcessingSettings;
use app\models\reference\Robot;
use app\models\register\Parsing;
use yii;

class FileSettingsController extends ActiveController
{
    public $modelClass          = "app\\models\\reference\\FileProcessingSettings";
    public $searchModelClass    = "app\\models\\reference\\FileProcessingSettings";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            //'index'         => null,
            'view'          => null,
            'update'          => null,
            //'create'        => null,
        ]);
    }


    public function actionUpdate($id) {
        $request = Yii::$app->request;

        $model = FileProcessingSettings::findOne($id);

        if ($request->isPost && $post = $request->post()) {
            $model->load($post);

            if (!$model->validate()) {
                throw new yii\base\InvalidValueException(yii\helpers\Json::encode($model->getErrors()));
            }

            $model->save();

            return $this->redirect(['/file-settings']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionView($id) {

        $this->redirect(['update', 'id' => $id]);
    }






    public function indexActionButtons($actionColumn) {
        return array_merge(parent::indexActionButtons($actionColumn),[
            'delete' => function(){
                return null;
            },
            'view' => function(){
                return null;
            },
        ]);
    }
}