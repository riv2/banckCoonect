<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_058_create_table_prc_error extends Migration
{
    public function up()
    {
        $this->createTable('{{%error}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'name' => $this->text(),
            'message' => $this->text(),
            'hash' => $this->string()->notNull(),
            'code' => $this->string(),
            'kind' => $this->string(),
            'entity_row_id' => $this->string(),
            'error_type_id' => $this->integer()->notNull()->defaultValue('0'),
            'entity_type_id' => $this->integer(),
            'file' => $this->text(),
            'line' => $this->integer(),
            'backtrace' => $this->text(),
            'info' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);
        $this->addPk('{{%error}}', ['id']);

        $this->db->createCommand("CREATE INDEX ci_prc_error_created_at ON prc_error (created_at DESC);")->execute();
    }

    public function down()
    {
        $this->dropTable('{{%error}}');
    }
}
