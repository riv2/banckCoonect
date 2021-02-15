<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 18.01.19
 * Time: 13:08
 */

namespace App\Services;


use Illuminate\Support\Facades\Log;

class StepByStep
{
    /**
     * @param $routeName
     * @param $configName
     * @return mixed|null
     */
    static function nextRouteAfter($routeName, $configName)
    {
        $steps = config($configName . '.steps');

        foreach ($steps as $k => $step)
        {
            if($step == $routeName && isset($steps[$k + 1]))
            {
                return $steps[$k + 1];
            }
        }

        return null;
    }

    /**
     * @param $configName
     * @return mixed|null
     */
    static function firstRoute($configName)
    {
        $steps = config($configName . '.steps');

        if(isset($steps[0]))
        {
            return $steps[0];
        }

        return null;
    }
}