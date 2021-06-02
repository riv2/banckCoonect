<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200610_093018_add_cookie_domain extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing_project}}', 'cookies_domain', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing_project}}', 'cookies_domain');
    }
}
