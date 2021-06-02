<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Common\UsersController;

class AdvertisersController extends UsersController
{
    public $page_title = 'Список рекламодателей';
    public $role = 'advertiser';
}
