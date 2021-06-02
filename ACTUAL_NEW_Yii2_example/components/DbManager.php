<?php
/**
 * @link http://netis.pl/
 * @copyright Copyright (c) 2015 Netis Sp. z o. o.
 */

namespace app\components;

/**
 * Class DbManager tracks traversed path in the auth item tree.
 * @package netis\rbac
 */
class DbManager extends \netis\rbac\DbManager
{
    const ADMIN_ROLE = 'admin';

    /**
     * @inheritdoc
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        $roles = $this->getRolesByUser($userId);

        if ($roles) {
            foreach ($roles as $role) {
                if ($role->name == self::ADMIN_ROLE) {
                    return true;
                }
            }
        }
        return parent::checkAccess($userId, $permissionName, $params);
    }
}
