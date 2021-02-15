<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class StudyGroup
 * @package App
 * @property Profiles[]|Collection studentsProfiles
 * @property int specialityId
 */
class StudyGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'study_groups';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Profiles[]
     */
    public function studentsProfiles()
    {
        return $this->hasMany(Profiles::class)
            ->join('users', 'profiles.user_id', '=', 'users.id', 'inner')
            ->whereNull('users.deleted_at')
            ->where('profiles.education_status', Profiles::EDUCATION_STATUS_STUDENT)
            ->orderBy('fio');
    }

    public function getSpecialityIdAttribute() : ?int
    {
        /** @var Profiles $studentProfile */
        $studentProfile = $this->studentsProfiles()->first();

        return $studentProfile->education_speciality_id ?? null;
    }

    public static function getById(int $groupId) : ?self
    {
        return self::where('id', $groupId)->first();
    }

    /**
     * @param $disciplineIdList
     * @return array
     */
    public static function groupListByDisciplines($disciplineIdList){
        $resultGroupList = [];

        if($disciplineIdList)
        {
            $groupList = Profiles
                ::select(['study_groups.id as id', 'study_groups.name as name'])
                ->leftJoin('study_groups', 'study_groups.id', '=', 'profiles.study_group_id')
                ->leftJoin('students_disciplines', 'students_disciplines.student_id', '=', 'profiles.user_id')
                ->whereIn('students_disciplines.discipline_id', $disciplineIdList)
                ->groupBy(['study_groups.id', 'study_groups.name'])
                ->get();

            $excList = [];

            foreach ($groupList as $group)
            {
                $excList[] = $group->id;
                $resultGroupList[] = $group;
            }

            $resultGroupList[] =  (object)['id' => 0, 'name' => '----------------------'];

            $groupList2 = StudentGroupsSemesters
                ::select(['study_groups.id as id', 'study_groups.name as name'])
                ->leftJoin('study_groups', 'study_groups.id', '=', 'student_groups_semesters.study_group_id')
                ->leftJoin('students_disciplines', 'students_disciplines.student_id', '=', 'student_groups_semesters.user_id')
                ->whereIn('students_disciplines.discipline_id', $disciplineIdList)
                ->where('semester', '2019-20.1')
                ->whereNotIn('student_groups_semesters.study_group_id', $excList)
                ->groupBy(['study_groups.id', 'study_groups.name'])
                ->get();

            foreach ($groupList2 as $group)
            {
                $resultGroupList[] = $group;
            }
        }

        return $resultGroupList;
    }

    public function isExamTime($semesterString)
    {
        /** @var Profiles $studentProfile */
        $studentProfile = $this->studentsProfiles()->first();

        return $studentProfile->isExamTime($semesterString);
    }
}
