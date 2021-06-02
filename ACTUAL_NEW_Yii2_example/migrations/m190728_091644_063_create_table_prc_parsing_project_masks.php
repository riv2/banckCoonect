<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_063_create_table_prc_parsing_project_masks extends Migration
{
    public function up()
    {
        $this->createTable('{{%parsing_project_masks}}', [
            'id' => $this->uuidpk()->notNull(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'masks_id' => $this->uuid()->notNull(),
        ], null);

        $this->addPk('{{%parsing_project_masks}}', ['id']);
        //$this->addPk('{{%parsing_project_masks}}', ['parsing_project_id','id']);

    }

    public function down()
    {
        $this->dropTable('{{%parsing_project_masks}}');
    }
}
