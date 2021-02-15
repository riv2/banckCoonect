<?php
/**
 * User: dadicc
 * Date: 29.07.19
 * Time: 8:47
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    const LANGUAGE_EN = 'en';
    const LANGUAGE_RU = 'ru';
    const LANGUAGE_KZ = 'kz';

    public static $locales = [
        'ru',
        'en',
        'kz'
    ];

    protected $table = 'languages';

    /**
     * Get field name by locale
     * @param string $fieldName
     * @param string $currentLocale
     * @param string $defaultLocale
     * @param string $anotherLocale
     * @return string
     */
    public static function getFieldName(string $fieldName, string $currentLocale, string $defaultLocale = 'ru', string $anotherLocale = '') : string
    {
        // Default locale
        if ($currentLocale == $defaultLocale) {
            return $fieldName;
        }
        // Exist locale
        elseif (in_array($currentLocale, self::$locales)) {
            return $fieldName .'_'. $currentLocale;
        }
        // Another locale
        elseif ($anotherLocale == '') {
            return $fieldName;
        }
        else {
            return $fieldName .'_'. $anotherLocale;
        }
    }

}