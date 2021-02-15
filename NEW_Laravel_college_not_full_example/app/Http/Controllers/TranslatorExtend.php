<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Illuminate\Support\Facades\Log;

class TranslatorExtend extends Translator
{
    /**
     * @param string $key
     * @param array $replace
     * @param null $locale
     * @param bool $fallback
     *
     * @return array|null|string|void
     */
    //public function get($key, array $replace = [], $locale = null, $fallback = true)
    public function getFromJson($key, array $replace = [], $locale = null)
    {
        $translation = parent::getFromJson($key, $replace, $locale);
        
        if ($translation === $key && config('app.locale') != 'en') {
            Log::channel('notranslated')->info('Language item could not be found.', [
                'language' => $locale ?? config('app.locale'),
                'id' => $key,
                'url' => config('app.url')
            ]);
        }
        
        return $translation;
    }
}
