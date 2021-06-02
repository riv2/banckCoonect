<?php

use app\components\Migration;

class m191111_191644_add_horadric_cube_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%horadric_cube}}','predict', $this->float());
    }

    public function down()
    {
        $this->dropColumn('{{%horadric_cube}}','predict');
    }
}
