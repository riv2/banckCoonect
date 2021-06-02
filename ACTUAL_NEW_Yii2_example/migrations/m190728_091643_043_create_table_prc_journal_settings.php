<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_043_create_table_prc_journal_settings extends Migration
{
    public function up()
    {
        $this->createTable('{{%journal_settings}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'journal_id' => $this->string()->notNull(),
            'per_page' => $this->integer()->defaultValue('50'),
            'sort_order' => $this->text(),
            'enabled_columns' => $this->text(),
            'applied_filters' => $this->text(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
        ], null);

        $this->addPk('{{%journal_settings}}', ['id']);

        $this->createIndex('ux_prc_journal_settings_journal_id_created_user_id', '{{%journal_settings}}', ['created_user_id', 'journal_id'], true);
        $this->createIndex('ix_prc_journal_settings_journal_id', '{{%journal_settings}}', 'journal_id');
    }

    public function down()
    {
        $this->dropTable('{{%journal_settings}}');
    }
}
