<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Common\UsersController;

class PartnersController extends UsersController
{
    public $page_title = 'Список партнеров';
    public $role = 'partner';
}
