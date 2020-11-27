<?php
namespace frontend\models\forms;

use common\models\forms\LoginForm as BaseLoginForm;

/**
 * Форма входа.
 */
class LoginForm extends BaseLoginForm
{
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['password', 'safe'],
        ];
    }
}
