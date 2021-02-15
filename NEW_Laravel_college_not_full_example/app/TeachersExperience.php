<?php
/**
 * User: dadicc
 * Date: 30.07.19
 * Time: 7:29
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeachersExperience extends Model
{

    const TYPE_EXPERIENCE_PRACTICAL = 'practical';
    const TYPE_EXPERIENCE_TEACHING = 'teaching';
    const TYPE_EXPERIENCE_OTHER = 'other';

    const CURRENT_EXPERIENCE_YES = 'yes';
    const CURRENT_EXPERIENCE_NO = 'no';

    protected $table = 'teachers_experience';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date_from',
        'date_to',
        'workplace',
        'type_experience',
        'current_experience',
        'workstatus',
        'charges',
        'contacts',
    ];

}