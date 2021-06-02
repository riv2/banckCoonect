<?php
/**
 * Project qpartners
 * Created by danila 25.08.2020 @ 20:20
 */

namespace App\Helpers;

use App\Models\Pp;
use App\User;

class PartnerProgramStorage
{
    protected static $instance = null;

    /**
     * Получить ПП по домену
     * @param string|null $domain
     * @return null|Pp
     */
    public static function getPP($domain = null)
    {
        if (empty($domain)) {
            $domain = request()->getHost();
        }
        if (is_null(static::$instance) && !app()->runningUnitTests() && !app()->runningInConsole()) {
            static::$instance = Pp::query()
                ->where("tech_domain", "=", $domain)
                ->orWhere("prod_domain", "=", $domain)
                ->first();
        }
        if (empty(static::$instance)) {
            //меняем на false, чтобы больше не искать в базе
            static::$instance = false;
        }
        return static::$instance;
    }

    public static function setPP(Pp $pp)
    {
        static::$instance = $pp;
    }

    /**
     * Возвращает или ID владельца облачной партнерки, или ID менеджеров обычной
     * @return array
     */
    public static function getAdminsIds()
    {
        if (config("domain.cloud_domain")) {//Облако
            return [static::getPP()->user_id];
        } else {//Выделенная партнерка
            return User::query()
                ->where("role", "=", "manager")
                //->where("status","=",1) статусы у нас еще не определены
                //todo проверка статуса
                ->get(["id"])->pluck("id")->toArray();
        }
    }
}
