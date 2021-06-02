<?php

use app\components\Migration;

class m200401_105827_add_parsing_global_count_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%parsing}}', 'global_count', $this->integer()->defaultValue('0'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%parsing}}', 'global_count');
    }
}
