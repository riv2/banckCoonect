<?php
namespace frontend\controllers;

use frontend\models\forms\SignupForm;
use frontend\services\AuthService;
use frontend\services\SignupService;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\forms\LoginForm;

/**
 * Базовый контроллер.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Главная страница.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Авторизация пользователя.
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        /** @var AuthService $authService */
        $authService = \Yii::createObject(AuthService::class);
        /** @var LoginForm $loginForm */
        $loginForm = \Yii::createObject(LoginForm::class);

        if ($loginForm->load(Yii::$app->request->post()) && $authService->login($loginForm)) {
            return $this->goBack();
        }

        $loginForm->password = '';

        return $this->render('login', [
            'model' => $loginForm,
        ]);
    }

    /**
     * Выход пользователя.
     * @return mixed
     */
    public function actionLogout()
    {
        /** @var AuthService $authService */
        $authService = \Yii::createObject(AuthService::class);
        $authService->logout();

        return $this->goHome();
    }

    /**
     * Страница "О сайте".
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Регистрация пользователя.
     * @return mixed
     */
    public function actionSignup()
    {
        /** @var SignupService $signupService */
        $signupService = \Yii::createObject(SignupService::class);
        /** @var SignupForm $signupForm */
        $signupForm = \Yii::createObject(SignupForm::class);
        if ($signupForm->load(Yii::$app->request->post())) {
            if ($signupService->signup($signupForm)) {
                /** @var AuthService $authService */
                $authService = \Yii::createObject(AuthService::class);
                /** @var LoginForm $loginForm */
                $loginForm = \Yii::createObject(LoginForm::class);
                $loginForm->load(['email' => $signupForm->email, 'password' => $signupForm->password], '');
                if ($authService->login($loginForm)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $signupForm,
        ]);
    }
}
