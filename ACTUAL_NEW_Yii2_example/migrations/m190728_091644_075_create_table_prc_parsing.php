<?php

use app\components\Migration;

class m190728_091644_075_create_table_prc_parsing extends Migration
{
    public function up()
    {
        $this->createTable('{{%parsing}}', [
            'id' => $this->uuidpk()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'region_id' => $this->integer(),
            'domain' => $this->string(),
            'parsing_status_id' => $this->integer()->notNull()->defaultValue('1'),
            'total_count' => $this->integer()->defaultValue('0'),
            'parsed_count' => $this->integer()->defaultValue('0'),
            'errors_count' => $this->integer()->defaultValue('0'),
            'started_at' => $this->timestamp(),
            'finished_at' => $this->timestamp(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'source_id' => $this->integer(),
            'is_chain' => $this->boolean()->defaultValue(false),
            'scope_info' => $this->text(),
             'index' => $this->serial(),
            'requests_count' => $this->integer()->defaultValue('0'),
            'connected_count' => $this->integer()->defaultValue('0'),
            'success_count' => $this->integer()->defaultValue('0'),
            'unreached_count' => $this->integer()->defaultValue('0'),
            'name' => $this->string(),
            'settings_json' => $this->text(),
            'with_retries_count' => $this->integer()->defaultValue('0'),
            'regions' => $this->string(),
            'is_phantom' => $this->boolean()->notNull()->defaultValue(false),
            'in_stock_count' => $this->bigInteger()->notNull()->defaultValue('0'),
            'passed_filter_count' => $this->bigInteger()->notNull()->defaultValue('0'),
            'parsing_type' => $this->string()->notNull()->defaultValue('normal'),
            'prepare_pages' => $this->boolean()->notNull()->defaultValue(false),
            'robot_id' => $this->string(),
            'is_test' => $this->boolean()->notNull()->defaultValue(false),
            'parallel_droids' => $this->integer()->notNull()->defaultValue('1'),
            'hash' => $this->string(),
            'attempt' => $this->integer()->notNull()->defaultValue('1'),
            'next_attempt_id' => $this->uuid(),
            'droid_type' => $this->string()->defaultValue('p-droid'),
            'parallel_main_id' => $this->uuid(),
            'parallel_is_main' => $this->boolean()->notNull()->defaultValue('1'),
            'browser' => $this->string(),
        ], null);
        $this->addPk('{{%parsing}}', ['parsing_project_id', 'id']);
        $this->db->createCommand("CREATE INDEX ci_parsing_status_1 ON prc_parsing (status_id, is_test, created_at DESC) WHERE status_id = 0 AND is_test = true;")->execute();
        $this->db->createCommand("CREATE INDEX ci_parsing_status_test ON prc_parsing (is_test, status_id);")->execute();
        $this->db->createCommand("CREATE INDEX ci_parsing_status_created_at ON prc_parsing (created_at DESC) WHERE status_id = 0;")->execute();
        $this->createIndex('ix_prc_parsing_hash', '{{%parsing}}', 'hash');
        $this->createIndex('prc_parsing_id_uindex', '{{%parsing}}', 'id', true);
    }

    public function down()
    {
        $this->dropTable('{{%parsing}}');
    }
}
