<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_036_create_table_prc_exchange_export extends Migration
{
    public function up()
    {
        $this->createTable('{{%exchange_export}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'entity_id' => $this->uuid()->notNull(),
            'local_id' => $this->string()->notNull(),
            'exchange_system_id' => $this->uuid()->notNull(),
            'is_error' => $this->boolean()->notNull()->defaultValue(false),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);

        $this->addPk('{{%exchange_export}}', ['id']);
        $this->createIndex('ux_prc_exchange_export_exchange_system_id_entity_id_local_id', '{{%exchange_export}}', ['entity_id', 'local_id', 'exchange_system_id'], true);

        //$this->addPk('{{%exchange_export}}', ['entity_id','id']);
    }

    public function down()
    {
        $this->dropTable('{{%exchange_export}}');
    }
}
