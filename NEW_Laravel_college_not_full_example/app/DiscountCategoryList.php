<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DiscountCategoryList extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'discount_category_list';
}
