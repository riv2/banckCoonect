<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibraryReport extends Model
{
    protected $guarded = [];

    public static $action_types = [
    	'download',
    	'order'
    ];

    public static $statuses = [
    	'order' => 'Заказ литературы',
    	'pending' => 'Литература выдана',
    	'returned' => 'Литература возвращена'
    ];

    public function user(){
    	return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
