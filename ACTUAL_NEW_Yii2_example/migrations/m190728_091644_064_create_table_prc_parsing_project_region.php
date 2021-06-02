<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_064_create_table_prc_parsing_project_region extends Migration
{
    public function up()
    {
        $this->createTable('{{%parsing_project_region}}', [
            'id' => $this->uuidpk()->notNull(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'region_id' => $this->integer()->notNull(),
            'cookies' => $this->text(),
            'proxies' => $this->text(),
            'sort' => $this->integer()->defaultValue('0'),
             'index' => $this->serial(),
            'url_replace_from' => $this->string(),
            'url_replace_to' => $this->string(),
        ], null);
        $this->addPk('{{%parsing_project_region}}', ['id']);
        //$this->addPk('{{%parsing_project_region}}', ['parsing_project_id','id']);
    }

    public function down()
    {
        $this->dropTable('{{%parsing_project_region}}');
    }
}
