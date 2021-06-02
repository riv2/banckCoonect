<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_072_create_table_prc_project_price_former_type extends Migration
{
    public function up()
    {
        $this->createTable('{{%project_price_former_type}}', [
            'id' => $this->uuidpk()->notNull(),
            'project_id' => $this->uuid()->notNull(),
            'price_former_type_id' => $this->uuid()->notNull(),
        ], null);
        $this->addPk('{{%project_price_former_type}}', ['id']);
        //$this->addPk('{{%project_price_former_type}}', ['project_id', 'id']);
    }

    public function down()
    {
        $this->dropTable('{{%project_price_former_type}}');
    }
}
