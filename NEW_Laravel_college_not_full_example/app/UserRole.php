<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserRole extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'user_role';
}
