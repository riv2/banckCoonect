<?php

namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use yii;
use nineinchnick\usr\components;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{users}}".
 *
 * @property string id
 * @property string password
 * @property string email
 * @property string username
 * @property string name
 * @property string shortName
 * @property string firstname
 * @property string lastname
 * @property string activation_key
 * @property string access_token
 * @property string auth_key
 * @property \app\components\DateTime $created_at
 * @property \app\components\DateTime $updated_at
 * @property \app\components\DateTime $last_visit_at
 * @property \app\components\DateTime $password_set_at
 * @property boolean email_verified
 * @property boolean is_active
 * @property boolean is_disabled
 */
class User extends Reference
    implements
    components\IdentityInterface,
    components\PasswordHistoryIdentityInterface,
    components\ActivatedIdentityInterface,
    components\EditableIdentityInterface,
    components\ManagedIdentityInterface
{
    /**
     * @var Role[]
     */
    public $roles = [];

    /**
     * @var string ключ сессии для хранения списка каналов, на которые подписан пользователь
     */
    public $sessionKeyForWsChannels = 'ws-channels';

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Пользователь';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Пользователи';
    }

    /**
     * @inheritdoc
     */
    public function filteringRules()
    {
        return array_merge(parent::filteringRules(), [
            [['username', 'user_id', 'firstname', 'lastname', 'email'], 'trim'],
            [['username', 'user_id', 'firstname', 'lastname', 'email'], 'default'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                [['name'], 'string'],
                [['created_user_id', 'updated_user_id','id'], 'number'],
                [['username', 'email', 'firstname', 'lastname'], 'safe'],
                [['auth_key', 'activation_key', 'access_token'], 'safe'],
                [['is_active', 'is_disabled', 'email_verified'], 'boolean'],
                [['username'], 'string'],
                [['username'], 'unique', 'except' => self::SCENARIO_SEARCH],
                [['roles'], 'safe'],
            ],
            ValidationRules::ruleDateTime('created_at', 'updated_at', 'last_visit_at', 'password_set_at'),
            [],
            []
        );
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'id' => 'ID',
            'username' => 'Юзернейм',
            'password' => 'Пароль',
            'email' => 'Email',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'auth_key' => 'Auth Key',
            'activation_key' => 'Activation Key',
            'access_token' => 'Access Token',
            'last_visit_at' => 'Последний визит',
            'password_set_at' => 'Пароль установлен',
            'email_verified' => 'Email проверен',
            'is_active' => 'Доступ в Pricing',
            'is_disabled' => 'Заблокирован',
            'roles' => 'Роли',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function quickSearchTokenSetup($token, $query)
    {
        $query = parent::quickSearchTokenSetup($token, $query);
        $this->setAttribute('name', null);
        $this->setAttribute('lastname', $token);
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return array_merge(
            parent::crudIndexColumns(),
            [
                'username',
                'firstname',
                'lastname',
                'is_active',
                'email',
            ]
        );
    }


    /**
     * Получение списка каналов, на которые подписан пользователь
     * @return array
     */
    public function getWsChannels()
    {
        return Yii::$app->session->get($this->sessionKeyForWsChannels, []);
    }

    /**
     * Установка списка каналов, на которые подписан пользователь
     * @param array $channels
     */
    public function setWsChannels($channels)
    {
        Yii::$app->session->set($this->sessionKeyForWsChannels, $channels);
    }

    /**
     * Фамилия И.О.
     * @return string
     */
    public function getShortName() {
        return $this->lastname.' '.mb_strtoupper(preg_replace("/([\S])[\S]+/iu","$1.", $this->firstname, 2));
    }

    public function __toString()
    {
        return $this->shortName;
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $this->name = $this->username;
        return parent::beforeValidate();
    }

    public function getName() {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->name = $this->username;
        if ($insert) {
            $this->id = Yii::$app->db->createCommand('SELECT nextval(\'prc_user_id_seq\')')->queryScalar();
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
        }

        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * Finds an identity by the given name.
     *
     * @param  string                 $username the name to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findByUsername($username)
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * @param $password
     * @return bool
     * @throws \Adldap\Exceptions\AdldapException
     */
    public function verifyPassword($password)
    {
        try {
            if (Yii::$app->security->validatePassword($password, $this->password)) {
                return true;
            }
            /** @var \Adldap\Adldap $ldap */
            $ldap = Yii::$app->ldap;
            return $ldap->authenticate($this->username, $password, true);
        } catch (\yii\base\InvalidParamException $e) {
            return false;
        }
    }

    // IdentityInterface

    /**
     * Finds an identity by the given ID.
     *
     * @param  string|integer         $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * Finds an identity by the given secrete token.
     *
     * @param  string                $token the secrete token
     * @param  mixed                 $type  the type of the token. The value of this parameter depends on the implementation.
     * @return IdentityInterface     the identity object that matches the given token.
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param  string  $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function authenticate($password)
    {
        if (!$this->is_active) {
            return [self::ERROR_INACTIVE, Yii::t('usr', 'Вашей учетной записи не дан доступ в Прайсинг. Обратитесь в отдел ценообразования.')];
        }
        if ($this->is_disabled) {
            return [self::ERROR_DISABLED, Yii::t('usr', 'Вашей учетной записи не дан доступ в Прайсинг. Обратитесь в отдел ценообразования.')];
        }
        if (!$this->verifyPassword($password)) {
            return [self::ERROR_INVALID, Yii::t('usr', 'Не верный логин или пароль')];
        }

        $this->last_visit_at = date('Y-m-d H:i:s');
        $this->save(false);

        return true;
    }

    /**
     * Получение имени персонального ws-канала
     * @return string
     */
    public function getPersonalWsChannelName()
    {
        return 'user#' . $this->id;
    }

    // }}}

    // {{{ PasswordHistoryIdentityInterface

    /**
     * Returns the date when specified password was last set or null if it was never used before.
     * If null is passed, returns date of setting current password.
     * @param  string $password new password or null if checking when the current password has been set
     * @return string date in YYYY-MM-DD format or null if password was never used.
     */
    public function getPasswordDate($password = null)
    {
        if ($password === null) {
            return $this->password_set_at;
        }

        return null;
    }

    /**
     * Changes the password and updates last password change date.
     * Saves old password so it couldn't be used again.
     * @param  string  $password new password
     * @return boolean
     */
    public function resetPassword($password)
    {
        $hashedPassword = Yii::$app->security->generatePasswordHash($password);
        $this->setAttributes([
            'password' => $hashedPassword,
            'password_set_at' => date('Y-m-d H:i:s'),
        ], false);

        //return $usedPassword->save() && $this->save();
        return $this->save();
    }

    // }}}

    // {{{ EditableIdentityInterface

    /**
     * Maps the \nineinchnick\usr\models\ProfileForm attributes to the identity attributes
     * @see \nineinchnick\usr\models\ProfileForm::attributes()
     * @return array
     */
    public function identityAttributesMap()
    {
        // notice the capital N in username
        return ['username' => 'username', 'email' => 'email', 'firstName' => 'firstname', 'lastName' => 'lastname'];
    }

    /**
     * Saves a new or existing identity. Does not set or change the password.
     * @see PasswordHistoryIdentityInterface::resetPassword()
     * Should detect if the email changed and mark it as not verified.
     * @param  boolean $requireVerifiedEmail
     * @return boolean
     */
    public function saveIdentity($requireVerifiedEmail = false)
    {
        Yii::warning('Юхеры только из ЛДАП', 'usr');
        return false;
    }

    /**
     * Sets attributes like name, email, first and last name.
     * Password should be changed using only the resetPassword() method from the PasswordHistoryIdentityInterface.
     * @param  array   $attributes
     * @return boolean
     */
    public function setIdentityAttributes(array $attributes)
    {
        $allowedAttributes = $this->identityAttributesMap();
        foreach ($attributes as $name => $value) {
            if (isset($allowedAttributes[$name])) {
                $key = $allowedAttributes[$name];
                $this->$key = $value;
            }
        }

        return true;
    }

    /**
     * Returns attributes like name, email, first and last name.
     * @return array
     */
    public function getIdentityAttributes()
    {
        $allowedAttributes = array_flip($this->identityAttributesMap());
        $result = [];
        foreach ($this->getAttributes() as $name => $value) {
            if (isset($allowedAttributes[$name])) {
                $result[$allowedAttributes[$name]] = $value;
            }
        }

        return $result;
    }

    // }}}

    // {{{ ActivatedIdentityInterface

    /**
     * Checks if user account is active. This should not include disabled (banned) status.
     * This could include if the email address has been verified.
     * Same checks should be done in the authenticate() method, because this method is not called before logging in.
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * Checks if user account is disabled (banned). This should not include active status.
     * @return boolean
     */
    public function isDisabled()
    {
        return (bool) $this->is_disabled;
    }

    /**
     * Checks if user email address is verified.
     * @return boolean
     */
    public function isVerified()
    {
        return (bool) $this->email_verified;
    }

    /**
     * Generates and saves a new activation key used for verifying email and restoring lost password.
     * The activation key is then sent by email to the user.
     *
     * Note: only the last generated activation key should be valid and an activation key
     * should have it's generation date saved to verify it's age later.
     *
     * @return string
     */
    public function getActivationKey()
    {
        $this->activation_key = Yii::$app->security->generateRandomKey();

        return $this->save(false) ? $this->activation_key : false;
    }

    /**
     * Verifies if specified activation key matches the saved one and if it's not too old.
     * This method should not alter any saved data.
     * @param  string  $activationKey
     * @return integer the verification error code. If there is an error, the error code will be non-zero.
     */
    public function verifyActivationKey($activationKey)
    {
        return $this->activation_key === $activationKey ? self::ERROR_AKEY_NONE : self::ERROR_AKEY_INVALID;
    }

    /**
     * Verify users email address, which could also activate his account and allow him to log in.
     * Call only after verifying the activation key.
     * @param  boolean $requireVerifiedEmail
     * @return boolean
     */
    public function verifyEmail($requireVerifiedEmail = false)
    {
        if ($this->email_verified) {
            return true;
        }
        $this->email_verified = 1;

        return $this->save(false);
    }

    /**
     * Returns user email address.
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    // }}}

    // {{{ ManagedIdentityInterface

    /**
     * @inheritdoc
     */
    public function getDataProvider(\nineinchnick\usr\models\SearchForm $searchForm)
    {
        $query = self::find();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere([
            'id'             => $searchForm->id,
            'created_at'     => $searchForm->createdOn,
            'updated_at'     => $searchForm->updatedOn,
            'last_visit_at'  => $searchForm->lastVisitOn,
            'email_verified' => $searchForm->emailVerified,
            'is_active'      => $searchForm->isActive,
            'is_disabled'    => $searchForm->isDisabled,
        ]);

        //! @todo add lowercase filter
        $query->andFilterWhere(['like', 'username', $searchForm->username])
            ->andFilterWhere(['like', 'firstname', $searchForm->firstName])
            ->andFilterWhere(['like', 'lastname', $searchForm->lastName])
            ->andFilterWhere(['like', 'email', $searchForm->email]);

        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function toggleStatus($status)
    {
        switch ($status) {
            case self::STATUS_EMAIL_VERIFIED: $this->email_verified = !$this->email_verified; break;
            case self::STATUS_IS_ACTIVE: $this->is_active = !$this->is_active; break;
            case self::STATUS_IS_DISABLED: $this->is_disabled = !$this->is_disabled; break;
        }

        return $this->save(false);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamps($key = null)
    {
        $timestamps = [
            'createdOn' => $this->created_at,
            'updatedOn' => $this->updated_at,
            'lastVisitOn' => $this->last_visit_at,
            'passwordSetOn' => $this->password_set_at,
        ];
        // can't use isset, since it returns false for null values
        return $key === null || !array_key_exists($key, $timestamps) ? $timestamps : $timestamps[$key];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        $roles = $this->roles;
        if (is_string($roles)) {
            $roles = explode(',', $roles);
        }
        $roles = array_filter($roles);

        $oldAssignedRoles = Yii::$app->authManager->getRolesByUser($this->id);
        $rolesForRemove = array_diff_key($oldAssignedRoles, array_flip($roles));
        $rolesForAdd = array_diff($roles, array_keys($oldAssignedRoles));
        foreach ($rolesForRemove as $role) {
            Yii::$app->authManager->revoke($role, $this->id);
        }
        foreach ($rolesForAdd as $roleId) {
            /** @var Role $role */
            $role = Role::findOne($roleId);
            if ($role) {
                Yii::$app->authManager->assign($role, $this->id);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->roles = array_keys(Yii::$app->authManager->getRolesByUser($this->id));
    }

    /**
     * @return int[]
     */
    public function getRolesIds()
    {
        return Role::find()
            ->andWhere([
                'name' => $this->roles,
            ])
            ->column();
    }

    // }}}
}