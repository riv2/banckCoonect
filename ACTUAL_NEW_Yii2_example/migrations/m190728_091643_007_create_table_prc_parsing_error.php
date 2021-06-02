<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_007_create_table_prc_parsing_error extends Migration
{
    public function up()
    {
        $this->createTable('{{%parsing_error}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'message' => $this->text(),
            'url' => $this->string(1024),
            'hash1' => $this->string()->notNull(),
            'hash2' => $this->string()->notNull(),
            'parsing_id' => $this->uuid()->notNull()->defaultValue('aaa00002-36e5-4b35-bd74-cb971a8d9335'),
            'parsing_project_id' => $this->uuid()->notNull()->defaultValue('aaa00001-36e5-4b35-bd74-cb971a8d9335'),
            'region_id' => $this->integer(),
            'robot_id' => $this->string(),
            'item_id' => $this->uuid(),
            'masks_id' => $this->uuid(),
            'competitor_id' => $this->uuid(),
            'proxy' => $this->string(),
            'user_agent' => $this->string(),
            'item' => $this->text(),
            'info' => $this->text(),
            'type' => $this->string(),
            'regions' => $this->string(),
        ], null);

        $this->db->createCommand("CREATE INDEX gin_parsing_error_date ON {{%parsing_error}} (parsing_project_id , created_at DESC);")->execute();
        $this->db->createCommand("CREATE INDEX gin_parsing_error_url ON {{%parsing_error}} USING gin ( parsing_project_id , url gin_trgm_ops);")->execute();

        $this->addPk('{{%parsing_error}}', ['parsing_project_id','id']);
    }

    public function down()
    {
        $this->dropTable('{{%parsing_error}}');
    }
}
