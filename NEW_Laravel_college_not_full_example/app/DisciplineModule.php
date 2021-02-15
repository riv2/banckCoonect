<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DisciplineModule extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'discipline_module';
}
