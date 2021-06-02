<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_006_create_table_prc_parsing_buffer extends Migration
{
    public function up()
    {
        $this->createTable('{{%parsing_buffer}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'buffer' => $this->text(),
            'is_error' => $this->boolean()->notNull()->defaultValue(false),
            'error_message' => $this->text(),
        ], null);
        $this->addPk('{{%parsing_buffer}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%parsing_buffer}}');
    }
}
