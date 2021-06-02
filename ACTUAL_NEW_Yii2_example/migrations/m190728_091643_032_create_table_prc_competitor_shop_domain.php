<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_032_create_table_prc_competitor_shop_domain extends Migration
{
    public function up()
    {
        $this->createTable('{{%competitor_shop_domain}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'competitor_id' => $this->uuid()->notNull(),
            'region_id' => $this->integer(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%competitor_shop_domain}}', ['id']);
        //$this->addPk('{{%competitor_shop_domain}}', ['competitor_id', 'id']);
        $this->db->createCommand("CREATE INDEX ux_prc_competitor_shop_domain_name ON {{%competitor_shop_domain}} USING gin (name gin_trgm_ops);")->execute();
        $this->createIndex('ux_prc_competitor_shop_domain_competitor_id_name', '{{%competitor_shop_domain}}', ['competitor_id','name'], true);
    }

    public function down()
    {
        $this->dropTable('{{%competitor_shop_domain}}');
    }
}
