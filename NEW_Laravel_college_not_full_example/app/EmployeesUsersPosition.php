<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmployeesUsersPosition
 * @package App
 *
 * @property int id
 * @property int user_id
 * @property int position_id
 * @property string schedule
 * @property string employment
 * @property string employment_form
 * @property int price
 * @property int salary
 * @property string organization
 * @property string payroll_type
 * @property Carbon probation_from
 * @property Carbon probation_to
 * @property Carbon created_at
 *
 * @property-read bool hasTeacherRole
 */
class EmployeesUsersPosition extends Model
{
	const EMPLOYMENT_MAIN = 'основная';
    const EMPLOYMENT_PART_TIME = 'совместительство';

    const EMPLOYMENT_FORM_MAIN = 'Штатный сотрудник';
    const EMPLOYMENT_FORM_PART_TIME = 'Сотрудник по совместительству';

	protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
		return $this->belongsTo('App\User');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position(){
		return $this->belongsTo('App\EmployeesPosition');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function perks(){
		return $this->hasMany('App\EmployeesUsersPerk', 'employees_position_id', 'id');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function roles(){
        return $this->hasManyThrough('App\Role', 'App\EmployeesPositionRole', 'position_id', 'id', 'position_id', 'role_id');
    }

    /**
     * @return bool
     */
    public function getHasTeacherRoleAttribute(): bool
    {
        $roles = $this->roles()->find(3);

        return isset($roles);
    }
}
