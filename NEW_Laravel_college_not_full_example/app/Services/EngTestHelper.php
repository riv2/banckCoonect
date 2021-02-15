<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 19.05.19
 * Time: 17:47
 */

namespace App\Services;


class EngTestHelper
{
    /**
     * @param $disciplineList
     * @param $levelId
     * @return null
     */
    static function getDisciplineIdByLevel($disciplineList, $levelId)
    {
        foreach ($disciplineList as $discipline)
        {
            if($discipline->discipline->language_level_id == $levelId)
            {
                return $discipline->discipline->id;
            }
        }

        return null;
    }

    /**
     * @param $disciplineList
     * @return mixed
     */
    static function getResult($disciplineList)
    {
        foreach ($disciplineList as $discipline)
        {
            if($discipline->final_result !== null)
            {
                return $discipline->final_result;
            }
        }

        return null;
    }

    /**
     * @param $disciplineList
     * @return mixed
     */
    static function getResultLetter($disciplineList)
    {
        foreach ($disciplineList as $discipline)
        {
            if($discipline->final_result !== null)
            {
                return $discipline->final_result_letter;
            }
        }

        return null;
    }
}