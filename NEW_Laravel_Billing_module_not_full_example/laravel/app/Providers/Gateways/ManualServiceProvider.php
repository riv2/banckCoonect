<?php
namespace App\Providers\Gateways;
use Illuminate\Support\ServiceProvider;


class ManualServiceProvider extends ServiceProvider
{

    public function __construct()
    {

    }

    public $type = 'MANUAL';


    public function payment()
    {
        return true;
    }


    public function result()
    {
        return true;
    }


    public function success()
    {
        return true;
    }


    public function fail()
    {
        return true;
    }

}
