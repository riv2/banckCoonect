<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_060_create_table_prc_file_exchange_settings extends Migration
{
    public function up()
    {
        $this->createTable('{{%file_exchange_settings}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'entity_id' => $this->integer()->notNull(),
            'columns_order' => $this->text(),
            'columns_values' => $this->text(),
            'preset_columns' => $this->text(),
            'exclude_columns' => $this->text(),
            'skip_first_row' => $this->boolean()->notNull()->defaultValue(false),
            'auto_mapping' => $this->boolean()->notNull()->defaultValue(false),
            'file_format_id' => $this->integer()->notNull()->defaultValue('1'),
            'data_source' => $this->string(),
            'is_export' => $this->boolean()->notNull()->defaultValue(false),
            'encoding' => $this->string(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%file_exchange_settings}}', ['id']);

    }

    public function down()
    {
        $this->dropTable('{{%file_exchange_settings}}');
    }
}
