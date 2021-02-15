<?php
/**
 * User: dadicc
 * Date: 23.07.19
 * Time: 16:52
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntDisciplineList extends Model
{

    protected $table = 'ent_discipline_list';

    protected $fillable = [
        'name'
    ];

}