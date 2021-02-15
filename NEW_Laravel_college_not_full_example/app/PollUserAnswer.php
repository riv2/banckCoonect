<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PollUserAnswer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'poll_users_answers';
}
