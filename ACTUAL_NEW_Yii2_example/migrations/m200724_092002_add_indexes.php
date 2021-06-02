<?php

use app\components\Migration;

class m200724_092002_add_indexes extends Migration
{
    public function safeUp()
    {
        $this->addIndex('{{%parsing_error}}', 'parsing_id');
        $this->addIndex('{{%price_parsed}}', 'parsing_id');
        $this->addIndex('{{%price_parsed}}', 'id');
    }

    public function safeDown()
    {
        $this->delIndex('{{%parsing_error}}', 'parsing_id');
        $this->delIndex('{{%price_parsed}}', 'parsing_id');
        $this->delIndex('{{%price_parsed}}', 'id');
    }
}
