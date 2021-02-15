<?php
/**
 * User: dadicc
 * Date: 2/24/20
 * Time: 12:01 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{DB, Log};

class Holidays extends Model
{

    protected $table = 'holidays';

    public $fillable = [
        'name',
        'date',
        'created_at'
    ];


    /**
     * test exist holiday day
     * @param $sDate
     * @return bool
     */
    public static function existDay($sDate)
    {

        $bExist = self::
        where('date', date('Y-m-d', strtotime($sDate)))->
        exists();
        if (!empty($bExist)) {
            return true;
        }
        return false;
    }

    /**
     * get working day or false
     * @param $sDate
     * @return bool|string
     */
    public static function getWorkingDay($sDate)
    {

        $currentDate = new \DateTime();
        $currentDate->setDate(date('Y', strtotime($sDate)), date('m', strtotime($sDate)), date('d', strtotime($sDate)));
        $bExist = self::where('date', $currentDate->format('Y-m-d'))->exists();

        $i = 0;
        while (!empty($bExist) && ($i < 30)) {
            $currentDate->add(new \DateInterval('P1D'));
            $bExist = self::where('date', $currentDate->format('Y-m-d'))->exists();

        }

        // find
        if (empty($bExist)) {
            return $currentDate->format('Y-m-d');
        }

        // not find
        return false;

    }
}
