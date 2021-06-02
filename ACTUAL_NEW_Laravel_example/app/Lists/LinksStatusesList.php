<?php
/**
 * Project qpartners
 * Created by danila 23.06.2020 @ 19:39
 */

namespace App\Lists;


class LinksStatusesList
{
    static function getList()
    {
        return [
            "ACTIVE"=>"Активна",
            "DELETED"=>"Заблокирована"
        ];
    }
}
