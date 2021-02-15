<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnterBuilding extends Model
{
	const DOOR_MAIN_IN = 'main_in';
	const DOOR_MAIN_OUT = 'main_out';

    protected $table = 'enter_building';


}
