<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m201207_025452_add_started_column_to_project extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%project}}', 'schedule_started', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%task}}', 'result_text', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%project}}', 'schedule_started');
        $this->dropColumn('{{%task}}', 'result_text');
    }
}
