<?php
/**
 * User: dadicc
 * Date: 4/9/20
 * Time: 6:16 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class NobdUserPc extends Model
{

    use SoftDeletes;

    protected $table = 'nobd_user_pc';

    protected $fillable = [
        "user_id",
        "nobd_user_id",
        "type_event",                                           // Вид мероприятия
        "type_direction",                                       // Вид направления
        "events",                                               // Уровень мероприятия
        "date_participation",                                   // Дата участия
        "reward"                                                // Награда
    ];


    /**
     * @param $value
     */
    public function setDateParticipationAttribute( $value )
    {
        if( !empty($value) && ($value != '') )
        {
            $this->attributes['date_participation'] = date('Y-m-d',strtotime( $value ));
        }
    }


    /**
     * @param $value
     */
    public function setTypeEventAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['type_event'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setTypeDirectionAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['type_direction'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setEventsAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['events'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setRewardAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['reward'] = $value;
        }
    }


}