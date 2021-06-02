<?php

namespace App\Lists;


class OffersMaterialsTypesList
{
    protected static $data = null;
    static function getList()
    {
        if (is_null(static::$data)) {
            $domain = config("app.domain");
            $cnf = config("offermaterials");
            static::$data = isset($cnf[$domain]) ? $cnf[$domain]["types"] : $cnf["default"]["types"];
        }
        return static::$data;
    }
}
