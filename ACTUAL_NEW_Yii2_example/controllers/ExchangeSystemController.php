<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 23.08.16
 * Time: 13:58
 */

namespace app\controllers;


use app\models\reference\ExchangeSystem;
use yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class ExchangeSystemController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {

        $request = Yii::$app->getRequest();
        
        if ($request->isPost && $request->post('id') && $post = $request->post()) {

            $exchangeSystem = ExchangeSystem::findOne( $request->post('id'));
            $exchangeSystem->load($post);

            if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
                $response = clone Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->content = json_encode(call_user_func_array('\yii\widgets\ActiveForm::validate', [$exchangeSystem]));
                return $response;
            }

            if (!$exchangeSystem->validate()) {
                throw new yii\base\InvalidValueException(yii\helpers\Json::encode($exchangeSystem->errors));
            }

            Yii::$app->session->setFlash("ExchangeSystem::Saved-".$exchangeSystem->id, "Изменения в настройках обмена сохранены");

            $exchangeSystem->save();

            return $this->redirect(['']);
        }

        return $this->render('index', [
            'systems'   => ExchangeSystem::find()->all()
        ]);
    }

}