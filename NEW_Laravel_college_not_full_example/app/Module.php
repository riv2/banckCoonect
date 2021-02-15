<?php

namespace App;

use App\Services\SearchCache;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Module extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'modules';
    protected $fillable = [
        'name',
        'name_kz',
        'name_en'
    ];


    public static $adminRedisTable = 'admin_modules';

    private static $adminAjaxColumnList = [
        'id',
        'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function submodules()
    {
        return $this->belongsToMany(Submodule::class);
    }



//    public static function getModuleWithDisciplines(int $moduleId)
//    {
//        return self::with('disciplines')->where('id', $moduleId)->first();
//    }

    public static function getModuleWithDisciplinesAndSubmodulesLikeDisciplines(int $moduleId)
    {
        $module = self::with('disciplines')->with('submodules')->where('id', $moduleId)->first();

        if (!empty($module->submodules)) {
            foreach ($module->submodules as $submodule) {
                $module->disciplines[] = $submodule;
            }

            unset($module->submodules);
        }

        return $module;
    }

    public static function getListForSpecialityEdit()
    {
        return self::with('disciplines')
            ->with('submodules')
            ->whereHas('disciplines')
            ->get();
    }

    public static function getListForAdmin(?string $search = '', int $start = 0, int $length = 10, int $orderColumn = 0, string $orderDirection = 'asc')
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $recordsTotal = SearchCache::totalCount(self::$adminRedisTable);

        $query = self::select(['id', 'name'])->orderBy($orderColumnName, $orderDirection);

        // Search string $search
        if (!empty($search)) {
            // Get ids
            $idList = SearchCache::searchFull(self::$adminRedisTable, $search);
            $query->whereIn('id', $idList);

            if (is_numeric($search)) {
                $query->orWhere('id', (int)$search);
            }

            $recordsFiltered = count($idList);
        } else {
            $recordsFiltered = $recordsTotal;
        }

        // Get result
        $filterResult = $query
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($filterResult as $user) {
            $data[] = [
                $user->id,
                $user->name,
                ''
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }
}
