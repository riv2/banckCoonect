<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class DiscountTypeList
 * @property int category_id
 * @property string name_ru
 * @package App
 */
class DiscountTypeList extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'discount_type_list';

    public static function getById(int $id) : self
    {
        return self::where('id', $id)->first();
    }
}
