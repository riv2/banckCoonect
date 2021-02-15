<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    protected $table = 'info';

    protected $fillable = [
        'title_ru',
        'title_kz',
        'title_en',
        'text_preview_ru',
        'text_preview_kz',
        'text_preview_en',
        'text_ru',
        'text_kz',
        'text_en',
        'is_important',
    ];

    public function getTitleAttribute() {
        $locale = app()->getLocale();

        return $this->{'title_' . $locale} ?? $this->title_ru;
    }

    public function getTextPreviewAttribute() {
        $locale = app()->getLocale();

        return $this->{'text_preview_' . $locale} ?? $this->text_preview_ru;
    }

    public function getTextAttribute() {
        $locale = app()->getLocale();

        return $this->{'text_' . $locale} ?? $this->text_ru;
    }
}
