<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Refund extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    const STATUS_REFERENCE = 'reference';
    const STATUS_NEW = 'new';
    const STATUS_PROCESSING = 'processing';
    const STATUS_BANK_PROCESSING = 'bank_processing';
    const STATUS_SUCCESS = 'success';
    const STATUS_RETURNED = 'returned';

    const IBAN_KZ = "KZ";

    const REFERENCE_PRICE = 500;
    const REFUND_SIZE = 10000;

    protected $table = 'refunds_list';

    //delay in minuts
    static private $attempts_delay = [1, 5, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 120];

    static function attemptDelayMins($attempt = 0)
    {
        $lastElement = count(self::$attempts_delay);
        if ($lastElement <= $attempt) {
            return end(self::$attempts_delay);
        } else {
            return self::$attempts_delay[$attempt];
        }
    }

    static function attemptDelay($attempt = 0, $updated_at)
    {
        return strtotime($updated_at) + (self::attemptDelayMins($attempt) * 60);
    }

}
