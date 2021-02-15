<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-05
 * Time: 12:09
 */

namespace App\Services;

use App\{Profiles,TransactionHistory};
use App\Services\{Service1C};
use Illuminate\Support\Facades\{DB,Log,Response};

class TransactionService
{


    /**
     * @param $iUserId
     * @return bool
     */
    public static function clearHistory( $iUserId )
    {

        if( !empty( $iUserId ) )
        {
            DB::
            table('transaction_history')->
            where('user_id',intval( $iUserId ))->
            delete();
        }

        return false;

    }


    /**
     * @param array $aData
     * @param int $aData
     * @return bool
     */
    public static function saveHistory( $aData, $iUserId )
    {

        if( !empty($aData) && (count($aData) > 0) )
        {
            foreach( $aData as $aItemData )
            {

                $oTransactionHistory = new TransactionHistory();
                $oTransactionHistory->fill([
                    'user_id' => $iUserId,
                    'iin'     => $aItemData['iin'],
                    'type'    => $aItemData['type'],
                    'code'    => $aItemData['operation_id'],
                    'name'    => $aItemData['opertion_name'],
                    'cost'    => $aItemData['cost'] * -1,
                    'date'    => $aItemData['datetime']
                ]);
                $oTransactionHistory->save();
                unset($oTransactionHistory);

            }
        }

        return false;
    }


    /**
     * @param string $sIin
     * @param string $sDateFrom
     * @param string $sDateTo
     * @return mixed
     */
    public static function addHistory( $sIin, $sDateFrom, $sDateTo  )
    {

        if( empty($sIin) || empty($sDateFrom) || empty($sDateTo) )
        {
            return false;
        }

        $oProfiles = Profiles::where('iin',$sIin)->first();
        $mResult = Service1C::getHistory( $sIin, $sDateFrom, $sDateTo );

        if( !empty($oProfiles) && is_array($mResult) )
        {

            self::clearHistory( $oProfiles->user_id );
            self::saveHistory( $mResult, $oProfiles->user_id );
            return true;
        }

        return false;

    }


}