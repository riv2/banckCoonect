<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int id
 * @property string name
 */
class OrderAction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'order_actions';
}
