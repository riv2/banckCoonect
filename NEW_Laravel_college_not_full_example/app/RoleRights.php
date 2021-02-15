<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RoleRights extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'role_rights';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function projectSection()
    {
        return $this->hasOne(ProjectSection::class, 'id', 'project_section_id');
    }
}
