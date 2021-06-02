<?php
namespace frontend\tests\unit\models;

use common\fixtures\UserFixture;
use common\models\User;
use common\models\forms\SignupForm;

class SignupFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;


    /*public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ]);
    }

    public function testCorrectSignup()
    {
        $model = new SignupForm([
            'phone' => '666666666666',
            'firstName' => 'some_first_name',
            'lastName' => 'some_last_name',
            'middleName' => 'some_middle_name',
            'password' => 'some_password',
        ]);

        $user = $model->signup();

        expect($user)->isInstanceOf(User::class);

        expect($user->first_name)->equals('some_username');
        expect($user->middle_name)->equals('some_username');
        expect($user->last_name)->equals('some_username');
        expect($user->phone)->equals('some_email@example.com');
        expect($user->validatePassword('some_password'))->true();
    }

    public function testNotCorrectSignup()
    {
        $model = new SignupForm([
            'phone' => '79265555555',
            'firstName' => 'Сергей',
            'middleName' => 'Сергеевич',
            'lastName' => 'Второй',
            'gender' => '0',
            'birthday' => '1976-12-04',
            'password' => 'some_password',
        ]);

        expect_not($model->signup());
        expect_that($model->getErrors('username'));
        expect_that($model->getErrors('email'));

        expect($model->getFirstError('username'))
            ->equals('This username has already been taken.');
        expect($model->getFirstError('email'))
            ->equals('This email address has already been taken.');
    }*/
}
