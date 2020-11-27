<?php

namespace api\tests\unit\models;

use api\services\AuthService;
use Yii;
use common\models\forms\LoginForm;
use common\fixtures\UserFixture;

/**
 * Login form test
 */
class LoginFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;


    /**
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ];
    }

    public function testLoginNoUser()
    {
        /** @var LoginForm $loginForm */
        $loginForm = Yii::createObject(LoginForm::class);
        /** @var AuthService $authService */
        $authService = Yii::createObject(AuthService::class);

        $loginForm->setAttributes([
            'phone' => '77766677777',
            'password' => 'not_existing_password',
        ]);

        expect('model should not login user', $authService->login($loginForm))->false();
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    public function testLoginWrongPassword()
    {
        /** @var LoginForm $loginForm */
        $loginForm = Yii::createObject(LoginForm::class);
        /** @var AuthService $authService */
        $authService = Yii::createObject(AuthService::class);

        $loginForm->setAttributes([
            'phone' => '7777777777',
            'password' => 'wrong_password',
        ]);

        expect('model should not login user', $authService->login($loginForm))->false();
        expect('error message should be set', $loginForm->errors)->hasKey('password');
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    public function testLoginCorrect()
    {
        /** @var LoginForm $loginForm */
        $loginForm = Yii::createObject(LoginForm::class);
        /** @var AuthService $authService */
        $authService = Yii::createObject(AuthService::class);

        $loginForm->setAttributes([
            'phone' => '666666777777',
            'password' => 'password_0',
        ]);

        expect('model should login user', $authService->login($loginForm))->true();
        expect('error message should not be set', $loginForm->errors)->hasntKey('password');
        expect('user should be logged in', Yii::$app->user->isGuest)->false();
    }
}
