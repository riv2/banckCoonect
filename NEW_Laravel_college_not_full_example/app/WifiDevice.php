<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-23
 * Time: 12:13
 */

namespace App;

use Illuminate\Database\Eloquent\{Log,Model,SoftDeletes};

class WifiDevice extends Model
{

    use SoftDeletes;
    protected $table = 'wifi_devices';



}