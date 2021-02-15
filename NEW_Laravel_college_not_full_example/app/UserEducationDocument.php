<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class UserEducationDocument extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    const LEVEL_SECONDARY = 'secondary';
    const LEVEL_SECONDARY_SPECIAL = 'secondary_special';
    const LEVEL_HIGHER = 'higher';

    protected $table = 'user_education_documents';

    protected $fillable = [
        'level',
        'doc_number',
        'doc_series',
        'institution_name',
        'date',
        'city',
        'supplement_file',
        'speciality',
        'degree',
        'kz_holder',
        'institution_type',
        'specialization',
        'nostrification',
        'nostrification_file'
    ];

    protected $dates = [
        'date'
    ];

    protected $attributes = [
        'supplement_file_name' => ''
    ];

    /**
     * @param $value
     */
    public function setSupplementFileAttribute($value)
    {
        $this->attributes['supplement_file_name'] = time() . '.' . $value->getClientOriginalExtension();
    }

    /**
     * @param $value
     */
    public function setNostrificationFileAttribute($value)
    {
        $this->attributes['nostrification_file_name'] = 'n' . time() . '.' . $value->getClientOriginalExtension();
    }

    /**
     * @param $value
     */
    public function setLevelAttribute($value)
    {
        if($value == 'none')
        {
            $this->level = null;
        }
        else
        {
            $this->level = $value;
        }
    }
}
