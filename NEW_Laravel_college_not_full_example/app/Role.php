<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const NAME_ADMIN = 'admin';
    const NAME_ADMIN_TEACHER = 'admin_teacher';
    const NAME_CLIENT = 'client';
    const NAME_TEACHER = 'teacher';
    const NAME_TEACHER_MIRAS = 'teacher_miras';
    const NAME_GUEST = 'guest';
    const NAME_AGITATOR = 'agitator';
    const NAME_LISTENER_COURSE = 'listener_course';
    const NAME_OAUK = 'oauk';

    protected $table = 'roles';
    protected $fillable = [
        'id',
        'name',
        'title_ru',
        'description',
        'can_set_pay_in_orcabinet'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rights()
    {
        return $this->hasMany(
            RoleRights::class,
            'role_id',
            'id'
        );
    }

    /**
     * @param $projectSection
     * @param $actionType
     * @return bool
     */
    public function hasRight($projectSection, $actionType)
    {
        $rights = $this->rights();

        if(is_numeric($projectSection))
        {
            $rights->where('project_section_id', $projectSection);
        }
        else
        {
            $rights->whereHas('projectSection', function($query) use($projectSection) {
                $query->where('url', $projectSection);
            });
        }

        return (bool)$rights->where('can_' . $actionType, true)->count();
    }

    /**
     * @param $rightList
     * @return bool
     */
    public function syncRights($rightList)
    {
        $tabooDelete = [];

        foreach ($rightList as $sectionId => $item)
        {
            $roleRight = $this->rights->where('project_section_id', $sectionId)->first();

            if(!$roleRight)
            {
                $roleRight = new RoleRights();
                $roleRight->role_id = $this->id;
                $roleRight->project_section_id = $sectionId;
            }

            $roleRight->can_read    = isset($item['read']) && $item['read'];
            $roleRight->can_create  = isset($item['create']) && $item['create'];
            $roleRight->can_edit    = isset($item['edit']) && $item['edit'];
            $roleRight->can_delete  = isset($item['delete']) && $item['delete'];
            $roleRight->save();

            $tabooDelete[] = $roleRight->id;
        }

        $this->rights()->whereNotIn('id', $tabooDelete)->delete();
        return true;
    }
}
