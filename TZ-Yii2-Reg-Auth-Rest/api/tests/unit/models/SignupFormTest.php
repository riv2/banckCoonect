<?php

namespace api\tests\unit\models;

use api\services\AuthService;
use api\services\SignupService;
use common\models\forms\SignupForm;
use Yii;
use common\models\forms\LoginForm;
use common\fixtures\UserFixture;

/**
 * Signup form test
 */
class SignupFormTest extends \Codeception\Test\Unit
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

    public function testSignupWrongPhone()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Петр',
            'middleName' => 'Петрович',
            'lastName' => 'Первый',
            'phone' => '795675757',
            'password' => '12hd6jke',
            'gender' => 0,
            'birthday' => '1956-12-04',
        ]);

        expect('model should not login user', $signupService->signup($signupForm))->false();
        expect('error message should be set', $signupForm->errors)->hasKey('phone');
    }

    public function testSignupWrongPassword()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Петр',
            'middleName' => 'Петрович',
            'lastName' => 'Первый',
            'phone' => '79567575775',
            'password' => '12hd',
            'gender' => 0,
            'birthday' => '1956-12-04',
        ]);

        expect('model should not signup user', $signupService->signup($signupForm))->false();
        expect('error message should be set', $signupForm->errors)->hasKey('password');
    }

    public function testSignupWrongFirstName()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Petr',
            'middleName' => 'Петрович',
            'lastName' => 'Первый',
            'phone' => '79567575775',
            'password' => '12hd6jke',
            'gender' => 0,
            'birthday' => '1956-12-04',
        ]);

        expect('model should not signup user', $signupService->signup($signupForm))->false();
        expect('error message should be set', $signupForm->errors)->hasKey('firstName');
    }

    public function testSignupWrongMiddleName()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Петр',
            'middleName' => 'Петрович-',
            'lastName' => 'Первый',
            'phone' => '79567575775',
            'password' => '12hd6jke',
            'gender' => 0,
            'birthday' => '1956-12-04',
        ]);

        expect('model should not signup user', $signupService->signup($signupForm))->false();
        expect('error message should be set', $signupForm->errors)->hasKey('middleName');
    }

    public function testSignupWrongLastName()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Петр',
            'middleName' => 'Петрович',
            'lastName' => 'первый',
            'phone' => '79567575775',
            'password' => '12hd6jke',
            'gender' => 0,
            'birthday' => '1956-12-04',
        ]);

        expect('model should not signup user', $signupService->signup($signupForm))->false();
        expect('error message should be set', $signupForm->errors)->hasKey('lastName');
    }

    public function testSignupWrongGender()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Петр',
            'middleName' => 'Петрович',
            'lastName' => 'Первый',
            'phone' => '79567575775',
            'password' => '12hd6jke',
            'gender' => 3,
            'birthday' => '1956-12-04',
        ]);

        expect('model should not signup user', $signupService->signup($signupForm))->false();
        expect('error message should be set', $signupForm->errors)->hasKey('gender');
    }

    public function testSignupWrongBirthday()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Петр',
            'middleName' => 'Петрович',
            'lastName' => 'Первый',
            'phone' => '79567575775',
            'password' => '12hd6jke',
            'gender' => 0,
            'birthday' => '195612-04',
        ]);

        expect('model should not signup user', $signupService->signup($signupForm))->false();
        expect('error message should be set', $signupForm->errors)->hasKey('birthday');
    }

    public function testSignupSuccess()
    {
        /** @var SignupForm $signupForm */
        $signupForm = Yii::createObject(SignupForm::class);
        /** @var SignupService $signupService */
        $signupService = Yii::createObject(SignupService::class);

        $signupForm->setAttributes([
            'firstName' => 'Петр',
            'middleName' => 'Петрович',
            'lastName' => 'Первый',
            'phone' => '79567575775',
            'password' => '12hd6jke',
            'gender' => 0,
            'birthday' => '1956-12-04',
        ]);

        expect('model should signup user', $signupService->signup($signupForm))->true();
    }
}
