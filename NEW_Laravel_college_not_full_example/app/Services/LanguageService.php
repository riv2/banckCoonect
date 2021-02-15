<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.04.19
 * Time: 17:36
 */

namespace App\Services;


class LanguageService
{
    const TYPE_NATIVE = 'native';
    const TYPE_SECOND = 'second';
    const TYPE_OTHER = 'other';

    const LANGUAGE_RU = 'ru';
    const LANGUAGE_KZ = 'kz';
    const LANGUAGE_EN = 'en';

    /**
     * @param $langType
     * @param $userEducationLang
     * @return string
     * @throws \Exception
     */
    public static function getByType(string $langType, string $userEducationLang) : string
    {
        if ($langType == self::TYPE_NATIVE) {
            return $userEducationLang;
        }
        elseif ($langType == self::TYPE_SECOND) {
            return (in_array($userEducationLang, [self::LANGUAGE_RU, self::LANGUAGE_EN])) ? self::LANGUAGE_KZ : self::LANGUAGE_RU;
        }
        elseif ($langType == self::TYPE_OTHER) {
            return self::LANGUAGE_EN;
        } else {
            throw new \Exception('Wrong language type');
        }
    }

    public static function getSecond(string $educationLang)
    {
        return self::getByType(self::TYPE_SECOND, $educationLang);
    }
}