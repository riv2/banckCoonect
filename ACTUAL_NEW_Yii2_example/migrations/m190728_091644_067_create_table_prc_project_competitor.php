<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_067_create_table_prc_project_competitor extends Migration
{
    public function up()
    {
        $this->createTable('{{%project_competitor}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'competitor_id' => $this->uuid()->notNull(),
            'project_id' => $this->uuid()->notNull(),
            'select_price_logic_id' => $this->integer()->notNull()->defaultValue('101'),
            'min_margin' => $this->double(),
            'price_variation_modifier' => $this->double(),
            'is_key_competitor' => $this->boolean()->notNull()->defaultValue(false),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'name' => $this->string(),
             'index' => $this->serial(),
            'price_final_modifier' => $this->integer(),
        ], null);

        $this->addPk('{{%project_competitor}}', ['id']);
        //$this->addPk('{{%project_competitor}}', ['project_id', 'id']);
        $this->db->createCommand("CREATE INDEX ci_prc_project_competitor_variation ON prc_project_competitor (project_id,competitor_id)
WHERE NOT(price_variation_modifier IS NULL);")->execute();
        $this->createIndex('ix_prc_project_competitor_competitor_id', '{{%project_competitor}}', 'competitor_id');
        $this->createIndex('ix_prc_project_competitor_project_id', '{{%project_competitor}}', 'project_id');
    }

    public function down()
    {
        $this->dropTable('{{%project_competitor}}');
    }
}
