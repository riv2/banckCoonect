<?php

namespace App\Providers;

use App\Helpers\PartnerProgramStorage;
use Gecche\Multidomain\Console\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewServiceProvider;

class TemplateServiceProvider extends ViewServiceProvider
{
    public function registerViewFinder()
    {
        $this->app->bind('view.finder', function ($app) {

            $paths = $app['config']['view.paths'];//По умолчанию будет искать в директории из конфига
            $custom_path = resource_path() . '/customviews/';

            if (PartnerProgramStorage::getPP()) {//Если это облачный домен
                if (file_exists(realpath($custom_path . PartnerProgramStorage::getPP()->pp_domain))) {//Если есть файл в директории с имененем домена
                    array_unshift($paths, realpath($custom_path . PartnerProgramStorage::getPP()->pp_domain));//Добавим в начало массива
                }
            } else {
                if (file_exists(realpath($custom_path . config('app.template')))) {//Если есть файл в директории из настроек проекта
                    array_unshift($paths,realpath($custom_path . config('app.template')));//Добавим в начало массива
                }
            }

            return new FileViewFinder($app['files'], $paths);
        });
    }
}
