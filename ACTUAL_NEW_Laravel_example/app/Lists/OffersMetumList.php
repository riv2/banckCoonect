<?php
/**
 * Project qpartners
 * Created by danila 10.07.2020 @ 8:51
 */

namespace App\Lists;


class OffersMetumList
{
    protected static $data = null;
    static function getList()
    {
        if (is_null(static::$data)) {
            $domain = config("app.domain");
            $cnf = config("offersmetum");
            static::$data = isset($cnf[$domain]) ? $cnf[$domain] : $cnf["default"];
        }
        return static::$data;
    }
}
