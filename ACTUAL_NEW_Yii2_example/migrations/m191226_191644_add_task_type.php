<?php

use app\components\Migration;

class m191226_191644_add_task_type extends Migration
{
    public function up()
    {
        $this->insert('{{%task_type}}', [
            'id' => 15,
            'name' => 'Отчет KPI по проекту',
            'icon' => 'fa fa-refresh',
        ]);
    }

    public function down()
    {
        $this->delete('{{%task_type}}', [
            'id' => 15,
        ]);
    }
}
