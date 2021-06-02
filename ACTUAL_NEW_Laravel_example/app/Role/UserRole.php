<?php

namespace App\Role;

/***
 * Class UserRole
 * @package App\Role
 */
class UserRole
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADVERTISER = 'advertiser';
    const ROLE_ANALYST = 'analyst';
    const ROLE_PARTNER = 'partner';

    /***
     * @return array
     */
    public static function getRoleList()
    {
        return [
            static::ROLE_ADMIN => 'Администратор',
            static::ROLE_MANAGER => 'Менеджер',
            static::ROLE_ADVERTISER => 'Рекламодатель',
            static::ROLE_ANALYST => 'Анализ',
            static::ROLE_PARTNER => 'Партнер',
        ];
    }
}
