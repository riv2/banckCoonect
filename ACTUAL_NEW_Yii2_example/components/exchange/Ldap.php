<?php
namespace app\components\exchange;
use app\components\DateTime;
use app\models\reference\User;
use yii;

class Ldap extends Exchange
{
    public $domain_controllers;
    public $ad_port;
    public $admin_password;
    public $admin_username;
    public $base_dn;
    public $account_suffix;

    public $lastImportUsers = null;

    private $defaultPassword = null;

    public static function systemName() {
        return 'LDAP';
    }

    /**
     * @inheritdoc
     */
    public function getLabels() {
        return array_merge([
            'lastImportUsers'           => 'Последний импорт Юзеров',
        ], parent::getLabels());
    }


    public function importUsersOne($entry) {
        $security = Yii::$app->security;

        $username = $entry['samaccountname'][0];
        /** @var User $user */
        $user = User::findOne([ 'username' => $username]);
        if (!$user) {
            $user = new User();
            $user->email    = isset($entry['mail'][0])?$entry['mail'][0]:'';
            $user->username     = $username;
            $user->password = $this->defaultPassword;
            $user->is_active = false;
            $user->is_disabled = true;
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $user->email_verified = true;
            }
            $user->created_at = gmdate('Y-m-d H:i:s');
            $user->access_token = $security->generateRandomString();
            $user->auth_key     = $security->generateRandomString();
            $user->firstname    = isset($entry['givenname'][0])?$entry['givenname'][0]:'';
            $user->lastname     = isset($entry['sn'][0])?$entry['sn'][0].' ':'';
            if ($user->validate()) {
                $user->save();
            } else {
                throw new \Exception(print_r($user->getErrors(),true));
            }
        }
        return $user;
    }

    public function importUsers() {
        $this->lastImportUsers = (new DateTime())->format(DateTime::DB_DATETIME_FORMAT);
        $security = Yii::$app->security;
        $hash = "sa%^693t3gga%56sal5356t";
        $this->defaultPassword = $security->generatePasswordHash($hash.md5($hash));

        $ldap = ldap_connect($this->domain_controllers[0], $this->ad_port);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($ldap, $this->admin_username, $this->admin_password);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        $pageSize = 100;
        $cookie = '';
        $count = 0;
        $users = [];
        do {
            ldap_control_paged_result($ldap, $pageSize, true, $cookie);
            $filter = '(objectClass=user)';
            $result  = ldap_search($ldap, $this->base_dn, $filter);
            $entries = ldap_get_entries($ldap, $result);
            array_shift($entries);
            $count += count($entries);
            foreach ($entries as $entry) {
                if (!isset($entry['samaccountname'][0])) {
                    continue;
                }
                if (strpos($entry['samaccountname'][0], '$') > -1) {
                    continue;
                }
                $username = $entry['samaccountname'][0];

                $users[$username] = $this->importOne('Users', $username, $entry);
            }
            ldap_control_paged_result_response($ldap, $result, $cookie);

        } while($cookie !== null && $cookie != '');

        return $users;
    }
}