<?php
namespace common\models\forms;

use yii\base\Model;
use common\models\User;

/**
 * Форма регистрации пользователя.
 */
class SignupForm extends Model
{
    /**
     * @var string Имя.
     */
    public $email;

    /**
     * @var string Имя.
     */
    public $firstName;

    /**
     * @var string Отчество.
     */
    public $middleName;

    /**
     * @var string Фамилия.
     */
    public $lastName;

    /**
     * @var string Номер телефона.
     */
    public $phone;

    /**
     * @var string Пароль.
     */
    public $password;

    /**
     * @var string Пол.
     */
    public $gender;

    /**
     * @var string Дата рождения.
     */
    public $birthday;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $nameExpression = '/^[А-Я]{1}[а-я]*\-?[а-я]+$/u';
        $passwordExpression = '/[A-Za-z0-9]*/';
        $phoneExpression = '/[0-9]{11,13}/';
        $emailExprasion = '/[0-9a-z]+@[a-z]/';

        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'string', 'max' => 255],
            ['email', 'match', 'pattern' => $emailExprasion],

            ['firstName', 'trim'],
            ['firstName', 'required'],
            ['firstName', 'string', 'max' => 255],
            ['firstName', 'match', 'pattern' => $nameExpression],

            ['middleName', 'trim'],
            ['middleName', 'string', 'max' => 255],
            ['middleName', 'match', 'pattern' => $nameExpression],

            ['lastName', 'trim'],
            ['lastName', 'required'],
            ['lastName', 'string', 'max' => 255],
            ['lastName', 'match', 'pattern' => $nameExpression],

            ['phone', 'trim'],
            ['phone', 'required'],
            ['phone', 'string'],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'Такой номер телефона уже используется.'],
            ['phone', 'match', 'pattern' => $phoneExpression],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password', 'match', 'pattern' => $passwordExpression],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Почта',
            'firstName' => 'Имя',
            'middleName' => 'Отчество',
            'lastName' => 'Фамилия',
            'phone' => 'Номер телефона',
            'password' => 'Пароль',
        ];
    }
}
