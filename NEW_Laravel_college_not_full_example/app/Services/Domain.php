<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 23.10.18
 * Time: 12:06
 */

namespace App\Services;


class Domain
{
    /**
     * @return string
     * @throws \Exception
     */
    static function getSubdomain()
    {   
        //are we running through CLI?
        if (strpos(php_sapi_name(), 'cli') !== false) {
            return '';
        }
        if( isset($_SERVER['HTTP_HOST']) ) {
            $host = str_replace('www.','', $_SERVER['HTTP_HOST']);
        }

        if(!$host) {
            throw new \Exception('Cant check subdomain: HTTP_HOST is required');
        }

        $parts = explode('.', $host);

        if( count($parts) < 3 )
        {
            return '';
        }

        return trim($parts[0]);
    }
}