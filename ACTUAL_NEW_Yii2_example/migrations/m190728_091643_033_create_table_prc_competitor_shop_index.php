<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_033_create_table_prc_competitor_shop_index extends Migration
{
    public function up()
    {
        $this->createTable('{{%competitor_shop_index}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'source_id' => $this->integer()->notNull()->defaultValue('1'),
            'competitor_id' => $this->uuid()->notNull(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%competitor_shop_index}}', ['id']);
        //$this->addPk('{{%competitor_shop_index}}', ['competitor_id', 'id']);
        $this->db->createCommand("CREATE INDEX ux_prc_competitor_shop_index_name ON {{%competitor_shop_index}} USING gin (name gin_trgm_ops);")->execute();
        $this->createIndex('ux_prc_competitor_shop_index_competitor_id_name', '{{%competitor_shop_index}}', ['competitor_id', 'source_id','name'], true);
    }

    public function down()
    {
        $this->dropTable('{{%competitor_shop_index}}');
    }
}
