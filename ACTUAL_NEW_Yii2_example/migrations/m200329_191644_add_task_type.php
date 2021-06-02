<?php

use app\components\Migration;

class m200329_191644_add_task_type extends Migration
{
    public function up()
    {
        $this->insert('{{%task_type}}', [
            'id' => 17,
            'name' => 'Подготовка расчета проекта',
            'icon' => 'fa fa-refresh',
        ]);
    }

    public function down()
    {
        $this->delete('{{%task_type}}', [
            'id' => 17,
        ]);
    }
}
