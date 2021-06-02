<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_010_create_table_prc_screenshot extends Migration
{
    public function up()
    {
        $this->createTable('{{%screenshot}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'item_id' => $this->uuid()->notNull(),
            'competitor_id' => $this->uuid(),
            'price' => $this->double(),
            'competitor_shop_name' => $this->string(),
            'url' => $this->string(1024),
            'filename' => $this->string()->notNull(),
            'public_url' => $this->string(),
            'is_published' => $this->boolean()->defaultValue(false),
            'rrp' => $this->double(),
            'parsing_id' => $this->uuid(),
             'index' => $this->serial(),
        ], null);
        $this->addPk('{{%screenshot}}', ['id']);

    }

    public function down()
    {
        $this->dropTable('{{%screenshot}}');
    }
}
