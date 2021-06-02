<?php

namespace app\components\base\type;
use app\components\base\BaseModel;
use app\components\ValidationRules;
use app\models\enum\Status;
use app\models\reference\User;

/**
 * @property string id
 * @property string name
 * @property int status_id
 * @property int created_user_id
 *
 * @property User   createdUser
 * @property Status status
 */
class Cross extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), ValidationRules::ruleUuid('id'));
    }

    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return true;
    }
}