<?php
/**
 * User: dadicc
 * Date: 4/7/20
 * Time: 4:07 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class NobdTypeEvent extends Model
{

    // вид мероприятия 39096

    use SoftDeletes;

    protected $table = 'nobd_type_event';

    protected $fillable = [
        "code",
        "name"
    ];


    /**
     * @param $page
     * @return mixed
     */
    public static function getList($page=null)
    {

        $iPage = ( !empty($page) && (intval($page) > 0) ) ? intval($page) : 1;

        return self::
        whereNull('deleted_at')->
        paginate(10, ['*'], 'page', $iPage);
    }


    /**
     * @param $iId
     * @return mixed
     */
    public static function getItem($iId)
    {
        return self::where('id',$iId)->whereNull('deleted_at')->first();
    }


    /**
     * @param $iId
     */
    public static function remove($iId)
    {
        $oRes = self::where('id',$iId)->whereNull('deleted_at')->delete();
    }


    /**
     * @param $iId
     * @return
     */
    public static function createOrFind($iId)
    {
        if( !empty($iId) && ($iId > 0) )
        {
            return self::where('id',$iId)->whereNull('deleted_at')->first();
        } else {
            $oModel = new self;
            $oModel->fill(['id'=>0,'code'=>'','name'=>'']);
            return $oModel;
        }
    }


}