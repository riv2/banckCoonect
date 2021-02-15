<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OrderName extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

	const ORDER_CODE_ENTER                 = "ЗЧ";
	const ORDER_CODE_TRANSFER_OTHER_UNIVER = "ПВ";
	
    protected $table = 'order_names';
}
