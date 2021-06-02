<?php
/**
 * @link http://netis.pl/
 * @copyright Copyright (c) 2015 Netis Sp. z o. o.
 */

namespace netis\rbac;

interface TraceableAuthManager
{
    /**
     * Returns a list of auth items between the one checked and the one assigned to the user,
     * after a successful checkAccess() call.
     * @return array
     */
    public function getCurrentPath();

    /**
     * This method is only used in \netis\utils\web\User.can() when loading cached results.
     * @param array $path
     * @return $this
     */
    public function setCurrentPath($path);
}

