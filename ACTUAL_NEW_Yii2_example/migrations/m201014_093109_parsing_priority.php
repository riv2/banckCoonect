<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m201014_093109_parsing_priority extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing}}', 'priority', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing}}', 'priority');
    }
}
