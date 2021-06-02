<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Common\UsersController;

class AnalystsController extends UsersController
{
    public $page_title = 'Список аналитиков';
    public $role = 'analyst';
}
