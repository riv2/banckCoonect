<?php

namespace App;


class Room
{
    /**
     * @param $listFromRequest
     * @return array
     */
    static function compareStuff($listFromRequest)
    {
        $result = [];
        foreach ($listFromRequest as $k => $stuff)
        {
            if($listFromRequest[$k] == '1')
                $result[] = $k;
        }

        return $result;
    }

    /**
     * @param array $roomStuff
     * @return array
     */
    static function getStuffIdList($roomStuff = [])
    {
        $idList = [];

        foreach ($roomStuff as $stuff) {
            $idList[] = $stuff->id;
        }

        return $idList;
    }
}
