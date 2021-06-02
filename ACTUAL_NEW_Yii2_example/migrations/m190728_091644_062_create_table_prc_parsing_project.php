<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091644_062_create_table_prc_parsing_project extends Migration
{
    public function up()
    {
        $this->createTable('{{%parsing_project}}', [
            'id' => $this->uuidpk()->notNull(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'source_id' => $this->integer(),
            'last_parsing_id' => $this->uuid(),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'competitor_id' => $this->uuid(),
             'index' => $this->serial(),
            'split_by' => $this->bigInteger(),
            'max_connections' => $this->integer(),
            'rate_limit' => $this->integer(),
            'retry_timeout' => $this->integer(),
            'timeout' => $this->integer(),
            'retries' => $this->integer(),
            'domain' => $this->string(),
            'proxies' => $this->text(),
            'user_agents' => $this->text(),
            'cookies' => $this->text(),
            'urls' => $this->text(),
            'blocked_domains' => $this->text(),
            'is_phantom' => $this->boolean()->notNull()->defaultValue(false),
            'is_our_regions' => $this->boolean()->defaultValue(false),
            'ping_url' => $this->string(1024),
            'parsing_type' => $this->string()->notNull()->defaultValue('normal'),
            'prepare_pages' => $this->boolean()->notNull()->defaultValue(false),
            'parallel_droids' => $this->integer()->notNull()->defaultValue('1'),
            'comment' => $this->text(),
            'restart_browser' => $this->integer()->notNull()->defaultValue('0'),
            'droid_type' => $this->string()->defaultValue('p-droid'),
            'vpn_provider' => $this->string(),
            'vpn_username' => $this->string(),
            'vpn_password' => $this->string(),
            'vpn_config' => $this->text(),
            'browser' => $this->string(),
            'used_by_calc' => $this->boolean()->defaultValue(false),
            'matching_api_enabled' => $this->boolean()->notNull()->defaultValue('1'),
        ], null);

        $this->addPk('{{%parsing_project}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%parsing_project}}');
    }
}
