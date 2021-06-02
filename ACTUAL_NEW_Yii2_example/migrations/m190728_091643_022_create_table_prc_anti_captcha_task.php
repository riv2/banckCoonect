<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_022_create_table_prc_anti_captcha_task extends Migration
{
    public function up()
    {
        $this->createTable('{{%anti_captcha_task}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'anti_captcha_task_id' => $this->string(),
            'answer' => $this->string(),
            'img_src' => $this->text(),
            'url' => $this->text(),
            'parsing_id' => $this->uuid()->notNull()->defaultValue('aaa00002-36e5-4b35-bd74-cb971a8d9335'),
            'parsing_project_id' => $this->uuid()->notNull()->defaultValue('aaa00001-36e5-4b35-bd74-cb971a8d9335'),
            'cost' => $this->double(),
            'error' => $this->string(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'img_body' => $this->text(),
        ], null);


        $this->db->createCommand("CREATE INDEX ci_prc_anti_captcha_task_Created_at ON {{%anti_captcha_task}} (parsing_id, created_at DESC);")->execute();

        //$this->addPk('{{%anti_captcha_task}}', ['id']);
        $this->addPk('{{%anti_captcha_task}}', ['parsing_project_id','id']);
    }

    public function down()
    {
        $this->dropTable('{{%anti_captcha_task}}');
    }
}
