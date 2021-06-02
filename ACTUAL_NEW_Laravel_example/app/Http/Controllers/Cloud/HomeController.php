<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;

/**
 * Контроллер главной страницы
 */
class HomeController extends Controller
{
    /**
     * Отображаем главную страницу облака
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(): Renderable
    {
        if (url()->previous() == 'https://gocpa.net') {
            App()->setLocale('en');
        }

        return view('gocpa_cloud/welcome');
    }

    /**
     * Отображаем главную страницу облака
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function already_registered(): Renderable
    {
        return view('gocpa_cloud/auth/login-advert');
    }
}
