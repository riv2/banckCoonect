<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200626_120356_add_save_browser_cookies extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing_project}}', 'save_browser_cookies', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing_project}}', 'save_browser_cookies');
    }
}
