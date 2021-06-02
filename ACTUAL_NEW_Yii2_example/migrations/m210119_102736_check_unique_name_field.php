<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m210119_102736_check_unique_name_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing_project}}', 'check_unique_name', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing_project}}', 'check_unique_name');
    }
}
