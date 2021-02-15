<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-19
 * Time: 19:24
 */

namespace App\Services;

use Illuminate\Support\Facades\Route;

class SidebarMenuActive {


    /**
     * @param string $sRouteName ( route name)
     * @return string
     */
    public static function isActive( string $sRouteName ) : string
    {

        return ( (string) Route::currentRouteName() == $sRouteName ) ? 'active' : '';
    }

}