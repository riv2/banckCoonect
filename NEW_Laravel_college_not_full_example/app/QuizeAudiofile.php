<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class QuizeAudiofile extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'quize_audiofiles';
}
