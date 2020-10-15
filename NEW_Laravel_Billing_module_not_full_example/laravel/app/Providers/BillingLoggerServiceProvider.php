<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\_billing_logs;

class BillingLoggerServiceProvider extends ServiceProvider
{

    const BILLING_LOGGER_TURN_ON = true;

    public function __construct()
    {
    }

    public static function addBillingLogRow($requester, $data, $uniqMember = null) {

        $responce_data = $data;

        if (self::BILLING_LOGGER_TURN_ON) {

            $status = 'unknown';

            if (isset($data['success'])) {
                if ($data['success'] === true) {
                    $data['success'] = 'true';
                    $status          = 'success';
                } else {
                    $data['success'] = 'false';
                    $status          = 'error';
                }
            }

            _billing_logs::create([
                'requester'      => $requester,
                'uniqMember'     => $uniqMember,
                'data'           => serialize($data),
                'date_created'   => BillingExtServiceProvider::getDateForTimestamp(),
                'advanced'       => null,
                'status'         => $status
            ]);
        }

        return $responce_data;
    }

}