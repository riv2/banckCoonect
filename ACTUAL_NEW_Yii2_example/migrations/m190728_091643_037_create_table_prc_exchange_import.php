<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_037_create_table_prc_exchange_import extends Migration
{
    public function up()
    {
        $this->createTable('{{%exchange_import}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'remote_id' => $this->string()->notNull(),
            'remote_entity' => $this->string()->notNull(),
            'is_error' => $this->boolean()->notNull()->defaultValue(false),
            'error_id' => $this->uuid(),
            'error_message' => $this->text(),
            'requester_entity_id' => $this->integer(),
            'requester_id' => $this->uuid(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%exchange_import}}', ['id']);
        $this->db->createCommand("CREATE INDEX ci_prc_exchange_import_remote_entity ON {{%exchange_import}} (remote_entity) WHERE is_error = false;")->execute();
        //$this->addPk('{{%exchange_import}}', ['entity_id','id']);
    }

    public function down()
    {
        $this->dropTable('{{%exchange_import}}');
    }
}
