<?php
namespace app\components;

use netis\crud\crud\ActiveNavigation;
use netis\crud\web\Response;
use yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Controller;

class BaseController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // bootstrap the ContentNegotiatot behavior earlier to use detected format for authenticator
        /** @var ContentNegotiator $contentNegotiator */
        $contentNegotiator = Yii::createObject([
            'class' => ContentNegotiator::className(),
            'formats' => [
                'text/html' => Response::FORMAT_HTML,
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
                // custom formats
                'text/csv' => Response::FORMAT_CSV,
                'application/pdf' => Response::FORMAT_PDF,
                'application/vnd.ms-excel' => Response::FORMAT_XLS,
            ],
        ]);
        $contentNegotiator->negotiate();
        return array_merge(parent::behaviors(), [
            'contentNegotiator' => $contentNegotiator,
            'authenticator' => [
                'class' => \yii\filters\auth\CompositeAuth::className(),
                'authMethods' => !Yii::$app->user->getIsGuest() || Yii::$app->response->format === Response::FORMAT_HTML
                    ? []
                    : [
                        \yii\filters\auth\HttpBasicAuth::className(),
                        \yii\filters\auth\QueryParamAuth::className(),
                    ],
            ],
            'rateLimiter' => [
                'class' => \yii\filters\RateLimiter::className(),
                'user' => Yii::$app->user->getIdentity(), // because the default doesn't autoRenew
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'menu' => [
                'class' => ActiveNavigation::className(),
            ],
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