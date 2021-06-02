<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_016_create_table_vw_prc_good_competitor extends Migration
{
    public function up()
    {
                $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vw_prc_good_competitor}}', [
            'pkid' => $this->char(),
            'name' => $this->string(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'source_id' => $this->integer(),
            'item_id' => $this->char(),
            'competitor_id' => $this->char(),
            'sku' => $this->string(),
            'url' => $this->char(),
            'status_id' => $this->integer(),
            'index' => $this->integer(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%vw_prc_good_competitor}}');
    }
}
