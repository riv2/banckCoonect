<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_039_create_table_prc_file_format extends Migration
{
    public function up()
    {
        $this->createTable('{{%file_format}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'mime_types' => $this->text(),
            'extensions' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
    }

    public function down()
    {
        $this->dropTable('{{%file_format}}');
    }
}
