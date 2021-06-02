<?php
/**
 * @link http://netis.pl/
 * @copyright Copyright (c) 2015 Netis Sp. z o. o.
 */

namespace netis\rbac;

/**
 * Class DbManager tracks traversed path in the auth item tree.
 * @package netis\rbac
 */
class PhpManager extends \yii\rbac\PhpManager implements TraceableAuthManager
{
    use AuthManagerTrait;
}
