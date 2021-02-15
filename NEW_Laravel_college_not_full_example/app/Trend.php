<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Trend extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'trends';

    protected $fillable = [
        'name',
        'name_kz',
        'name_en',
        'education_area_code',
        'training_code',
        'op_code',
        'classif_direction',
        'classif_direction_kz',
        'classif_direction_en',
    ];

    public function qualifications() {
        return $this->hasMany(TrendQualification::class);
    }

    public static function getIdsAndNames() : array
    {
        $aResponse = [];
        $oTrends = self::select(['id', 'name', 'education_area_code', 'training_code'])->get();
        if( !empty($oTrends) && (count($oTrends) > 0) )
        {
            foreach($oTrends as $item)
            {
                $aResponse[ $item->id ] = [
                    'name'                  => $item->name,
                    'education_area_code'   => $item->education_area_code,
                    'training_code'         => $item->training_code
                ];
            }
        }
        return $aResponse;
    }
}
