<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m190728_091643_014_create_table_prc_user extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->serial(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'username' => $this->string()->notNull(),
            'password' => $this->string()->notNull(),
            'email' => $this->string(),
            'firstname' => $this->string(),
            'lastname' => $this->string(),
            'auth_key' => $this->string(),
            'activation_key' => $this->string(),
            'access_token' => $this->string(),
            'last_visit_at' => $this->timestamp(),
            'password_set_at' => $this->timestamp(),
            'email_verified' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(false),
            'is_disabled' => $this->boolean()->notNull()->defaultValue(false),
            'status_id' => $this->integer()->notNull()->defaultValue('0'),
            'tenant_id' => $this->integer()->notNull()->defaultValue(1),
        ], null);

        $this->createIndex('prc_users_name_idx', '{{%user}}', 'username', true);
        $this->createIndex('prc_users_email_idx', '{{%user}}', 'email');
        $this->createIndex('prc_users_email_verified_idx', '{{%user}}', 'email_verified');
        $this->createIndex('prc_users_is_active_idx', '{{%user}}', 'is_active');
        $this->createIndex('prc_users_is_disabled_idx', '{{%user}}', 'is_disabled');

        $this->addPk('{{%user}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
