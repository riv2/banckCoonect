<?php
namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\reference\FileProcessingSettings;
use app\models\reference\Robot;
use app\models\register\FileProcessing;
use app\models\register\Parsing;
use yii;
use yii\web\Response;
use yii\web\UploadedFile;

class FileController extends ActiveController
{
    public $modelClass          = "app\\models\\register\\FileProcessing";
    public $searchModelClass    = "app\\models\\register\\FileProcessing";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            //'index'         => null,
            'view'          => null,
            'update'        => null,
            //'create'        => null,
        ]);
    }


    public function actionUpload($id = null) {
        $fp = new FileProcessing();

        $request = Yii::$app->request;

        if ($request->isPost && $post = $request->post()) {
            $fp->load($post);

            if (($response = $this->validateAjax($fp))) {
                return $response;
            }
            if (!$fp->validate()) {
                throw new yii\base\InvalidValueException(yii\helpers\Json::encode($fp->getErrors()));
            }

            $fps = FileProcessingSettings::findOne($fp->file_processing_settings_id);

            $fp->settings   = $fps->settings;

            $fp->uploadFile();
            $fp->save();
            $fp->createTask();

            return $this->redirect(['/file']);
        }
        if ($id) {
            $fp->file_processing_settings_id = $id;
        }
        return $this->render('upload', [
            'fileProcessing'    => $fp,
        ]);
    }

    /**
     * Calls ActiveForm::validate() on the model if current request is ajax and not pjax.
     * @param \app\components\base\BaseModel|array $model
     * @return Response returns boolean false if current request is not ajax or is pjax
     */
    protected function validateAjax($model)
    {
        if (!Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
            return false;
        }
        $response = clone Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        if (!is_array($model)) {
            $model = [$model];
        }
        $response->content = json_encode(call_user_func_array('\yii\widgets\ActiveForm::validate', $model));
        return $response;
    }



}