<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class AdminUserDiscipline
 * @package App
 * @property int id
 * @property int user_id
 * @property int discipline_id
 * @property Carbon created_at
 * @property bool kz_lang
 * @property bool ru_lang
 * @property bool en_lang
 * @property int employees_user_position_id
 *
 * @property-read Discipline discipline
 */
class AdminUserDiscipline extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'admin_user_discipline';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }
}
