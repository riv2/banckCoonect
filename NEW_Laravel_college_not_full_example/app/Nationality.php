<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Nationality extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'nationality_list';
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function nameByLocale()
    {
        $locale = app()->getLocale();

        if($locale == 'kz')
        {
            return $this->name_kz;
        }

        if($locale == 'ru')
        {
            return $this->name_ru;
        }

        return $this->name;
    }
}
