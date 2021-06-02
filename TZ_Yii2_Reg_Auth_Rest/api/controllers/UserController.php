<?php

namespace api\controllers;

use api\services\AuthService;
use api\services\SignupService;
use common\models\forms\SignupForm;
use common\models\forms\LoginForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Контроллер пользователя.
 */
class UserController extends BaseController
{
    /**
     * @inheritdoc
     */
    protected const AUTH_EXCEPT = ['login', 'signup'];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        return ArrayHelper::merge($behaviors, [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'signup' => ['POST', 'OPTIONS'],
                    'login' => ['POST', 'OPTIONS'],
                    'logout' => ['POST', 'OPTIONS'],
                ],
            ],
        ]);
    }

    /**
     * Регистрация пользователя.
     * @return SignupForm
     */
    public function actionSignup()
    {
        /** @var SignupService $signupService */
        $signupService = \Yii::createObject(SignupService::class);
        /** @var SignupForm $signupForm */
        $signupForm = \Yii::createObject(SignupForm::class);
        $signupForm->load(\Yii::$app->getRequest()->getBodyParams(), '');
        $signupService->signup($signupForm);

        return $signupForm;
    }

    /**
     * Вход пользователя.
     * @return LoginForm|null|\yii\web\IdentityInterface
     */
    public function actionLogin()
    {
        /** @var AuthService $authService */
        $authService = \Yii::createObject(AuthService::class);
        /** @var LoginForm $loginForm */
        $loginForm = \Yii::createObject(LoginForm::class);
        $loginForm->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($authService->login($loginForm)) {
            return \Yii::$app->user->identity;
        }

        return $loginForm;
    }

    /**
     * Выход пользователя.
     * @return bool
     */
    public function actionLogout()
    {
        /** @var AuthService $authService */
        $authService = \Yii::createObject(AuthService::class);
        $authHeader = \Yii::$app->request->getHeaders()->get('Authorization');

        return $authService->logout($authHeader);
    }
}
