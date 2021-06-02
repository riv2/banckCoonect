<?php

use app\components\Migration;

class m190821_191644_add_task_type extends Migration
{
    public function up()
    {
        $this->insert('{{%task_type}}', [
            'id' => 14,
            'name' => 'Обновление названий и цен Конкурентов',
            'icon' => 'fa fa-refresh',
        ]);
    }

    public function down()
    {
        $this->delete('{{%task_type}}', [
            'id' => 14,
        ]);
    }
}
