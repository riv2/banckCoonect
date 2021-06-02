<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

/**
 * Контроллер главной страницы
 */
class HomeController extends Controller
{
    /**
     * Метод главной страницы
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(): Renderable
    {
        return view('welcome');
    }
}
