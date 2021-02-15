<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-11
 * Time: 11:33
 */

namespace App\Factory;

use App\Services\Domain;
use Illuminate\View\Factory;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\{Log};

class MobileFactory extends Factory {
    public function make($view, $data = array(), $mergeData = array())
    {

        $subdomain = Domain::getSubdomain();

        $agent = new Agent();
        if( $agent->isMobile() && ($subdomain != 'admin') && ($subdomain != 't') && strpos($view, 'errors') === false)
        {

            try {

                return parent::make('mobile.' . $view, $data, $mergeData);

            } catch ( \Exception $e ){}

        }

        return parent::make($view, $data, $mergeData);

    }
}

