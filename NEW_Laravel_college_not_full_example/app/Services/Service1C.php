<?php


namespace App\Services;


use App\Holidays;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class Service1C
{
    const API_GET_BALANCE = 'site/hs/Balance/list';
    const API_REGISTRATION = 'site/hs/Client/Registration';
    const API_PAY = 'site/hs/Client/pay';
    const API_ORDER_LIST = 'site/hs/orders/list';
    const API_REFILL = 'site/hs/mails/pay';
    const API_REFUND = 'site/hs/Client/Refund';
    const API_ADD_TO_BALANCE = 'site/hs/mails/pay';
    const API_DELETE_PAY = 'site/ru_RU/hs/Client/Delete';

    const BANK_NAME_KASPI = 'KASPI';
    const BANK_NAME_SBER = 'SBER';

    const NOMENCLATURE_CODE_DISCIPLINE = '00000006539';

    /**
     * @param $apiUrl
     * @param $params
     */
    static function sendRequest($apiUrl, $params)
    {
        $apiHost = env('API_1C_HOST', '');
        $login = env('API_1C_LOGIN');
        $password = env('API_1C_PASSWORD');

        if(!$apiHost)
        {
            throw new \Exception('API_1C_HOST not found');
        }

        $apiUrl = 'http://' . $login . ':' . $password . '@' . $apiHost . '/' . $apiUrl;

        $response = Curl::to($apiUrl)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            ->withHeader('Content-Type: application/json')
            ->withData(json_encode($params))
            ->post();

        $response->content;
        Log::info('1c request', [
            'url' => $apiUrl,
            'status' => $response->status,
            'content' => $response->content,
            'request' => $params
        ]);

        return json_decode($response->content, true);
    }

    /**
     * @param $iin
     * @return bool
     */
    static function getBalance($iin)
    {
        if(!env('API_1C_ENABLED', false))
        {
            return false;
        }

        // Work on locale
        if (env('API_1C_EMULATED', false)) {
            return true;
        }

        $iin = is_array($iin) ? $iin : [$iin];
        $result = self::sendRequest(self::API_GET_BALANCE, $iin);
        $result = isset($result[0]['balance']) && is_numeric($result[0]['balance']) ? $result[0]['balance'] : false;

        if($result !== false)
        {
            return -1 * $result;
        }

        return false;
    }

    /**
     * @param $iin
     * @param $fullName
     * @param $gender
     * @param $bDate
     * @return bool
     * @throws \Exception
     */
    static function registration($iin, $fullName, $gender, $bDate)
    {
        if(!env('API_1C_ENABLED', false) || !$iin || strlen($iin) != 12)
        {
            return false;
        }

        // Work on locale
        if (env('API_1C_EMULATED', false)) {
            return true;
        }

        if($gender === 1)
        {
            $gender = 'male';
        }
        else
        {
            $gender = 'female';
        }

        $result = self::sendRequest(self::API_REGISTRATION, [
            'iin' => $iin,
            'full_name' => $fullName,
            'gender' => $gender,
            'bdate' => date('Y-m-d', strtotime($bDate))
        ]);

        if($result)
        {
            return true;
        }

        return false;
    }

    /**
     * @param $iin
     * @param $nomenclatureCode
     * @param $cost
     * @param string $idMirasApp
     * @return bool
     */
    static function pay($iin, $nomenclatureCode, $cost, $idMirasApp = ''): bool
    {
        if (!env('API_1C_ENABLED', false)) {
            return false;
        }

        // Work on locale
        if (env('API_1C_EMULATED', false)) {
            return true;
        }

        if ($cost === 0) {
            return true;
        }

        $startBalance = self::getBalance($iin);

        if ($startBalance === false) {
            return false;
        }

        // Low balance
        if (
            $startBalance < $cost &&
            !empty(Auth::user()) &&
            !Auth::user()->hasRole('admin') &&
            !Auth::user()->hasRole('teacher')
        ) {
            $user = User::getByIIN($iin);
            $user->balance = $startBalance;
            $user->save();

            if (!($user && $user->balanceByDebt() >= $cost)) {
                Log::info(
                    'Pay 1c error',
                    [
                        'iin' => $iin,
                        'balance' => $startBalance,
                        'cost' => $cost,
                        'id_miras_app' => $idMirasApp
                    ]
                );

                return false;
            }
        }

        $result = self::sendRequest(
            self::API_PAY,
            [
                'id_miras_app' => $idMirasApp,
                'iin_list' => is_array($iin) ? $iin : [$iin],
                'code' => $nomenclatureCode,
                'cost' => "$cost"
            ]
        );

        $endBalance = self::getBalance($iin);

        if ($endBalance === false || $endBalance == $startBalance) {
            return false;
        }

        if ($result && isset($result['Ошибка']) && $result['Ошибка'] === '') {
            return true;
        }

        return false;
    }

    /**
     * @param string $sIin
     * @param string $sDateFrom
     * @param string $sDateTo
     * @return mixed
     */
    public static function getHistory($sIin, $sDateFrom, $sDateTo)
    {

        if (!env('API_1C_ENABLED', false))
        {
            return false;
        }

        // Work on locale
        if (env('API_1C_EMULATED', false)) {
            return [];
        }

        if( empty($sIin) || empty($sDateFrom) || empty($sDateTo) )
        {
            return false;
        }

        $mResult = self::sendRequest(self::API_ORDER_LIST, [
            'iin'       => $sIin,
            'date_from' => date('d.m.Y',strtotime($sDateFrom)),
            'date_to'   => date('d.m.Y',strtotime($sDateTo))
        ]);

        if( is_array($mResult) )
        {
            return $mResult;
        }

        return false;

    }

    /**
     * @param string $iin
     * @param int $amount
     * @param string $payDocument
     * @return bool
     */
    public static function payDiscipline(string $iin, int $amount, $payDocument = '') : bool
    {
        return self::pay($iin, self::NOMENCLATURE_CODE_DISCIPLINE, $amount, $payDocument->id ?? '');
    }

    /**
     * @param $iin
     * @param $amount
     * @return array|bool
     * @throws \Exception
     */
    public static function addToBalance($iin, $amount, $bankName = 'KASPI', $date = null)
    {
        if (!env('API_1C_ENABLED', false))
        {
            return false;
        }

        // Work on locale
        if (env('API_1C_EMULATED', false)) {
            return true;
        }

        $date = $date ? date('d.m.Y', strtotime($date)) : date('d.m.Y', time());

        $result = self::sendRequest(self::API_ADD_TO_BALANCE, [
            'bank' => $bankName,
            'iin' => $iin,
            'summa' => $amount,
            'date' => $date
        ]);

        if( is_array($result) )
        {
            return $result;
        }

        return false;
    }

    /**
     * @param $payDocumentId
     * @return bool|mixed
     * @throws \Exception
     */
    public static function deletePay($payDocumentId, $digitId = true)
    {
        if (!env('API_1C_ENABLED', false))
        {
            return false;
        }

        // Work on locale
        if (env('API_1C_EMULATED', false)) {
            return [
                "УдалениеДокументаНачисление" => "true",
	            "Ошибка" => ""
            ];
        }

        if($digitId)
        {
            $payDocumentId = number_format($payDocumentId, 0, '.', ' ');
        }

        $result = self::sendRequest(self::API_DELETE_PAY, [
            'id_miras_app' => "$payDocumentId"
        ]);

        if( is_array($result) )
        {
            return $result;
        }

        return false;
    }

    /**
     * @return false|string|null
     */
    static function getBankDayAfterToday()
    {
        $payDate = new Carbon('now', 'Asia/Almaty');

        if( Holidays::existDay($payDate->format('Y-m-d')) )
        {
            $payDateStr = Holidays::getWorkingDay($payDate->format('Y-m-d'));
        }
        else
        {
            $payDateStr = $payDate->format('Y-m-d');
        }

        return !$payDateStr ? null : $payDateStr;
    }

}